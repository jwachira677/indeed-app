<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\Saving;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    public function eligibilityForm()
    {
        $members = User::orderBy('name')->get();
        $loanTypes = LoanType::active()->get();
        return view('loans.eligibility', compact('members', 'loanTypes'));
    }

    public function checkEligibility(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount'  => 'required|numeric|min:1',
            'loan_type' => 'required|exists:loan_types,slug',
        ]);

        $members = User::orderBy('name')->get();
        $loanTypes = LoanType::active()->get();
        $selectedUser = User::findOrFail($validated['user_id']);
        $selectedLoanType = LoanType::where('slug', $validated['loan_type'])->firstOrFail();

        $financialYear = Saving::getCurrentFinancialYear();

        // Get total savings
        $totalSavings = Saving::where('user_id', $selectedUser->id)
            ->where('financial_year', $financialYear)
            ->where('status', 'approved')
            ->sum('amount');

        // Get active loan balance
        $activeLoanBalance = Loan::where('user_id', $selectedUser->id)
            ->whereIn('status', ['approved', 'active'])
            ->get()
            ->sum(function ($loan) {
                return $loan->balance;
            });

        // Calculate available savings
        $availableSavings = max(0, $totalSavings - $activeLoanBalance);
        
        // Calculate loan limit (75% of available savings)
        $loanLimit = round($availableSavings * 0.75, 2);
        
        $requestedAmount = (float) $validated['amount'];
        $qualifies = $requestedAmount <= $loanLimit;

        // Get duration based on loan type and amount
        $durationDays = $selectedLoanType->getDurationForAmount($requestedAmount);
        
        // Calculate due date
        $dueDate = now()->addDays($durationDays);

        // Calculate interest
        $interestRate = $selectedLoanType->interest_rate;
        $interestAmount = ($requestedAmount * $interestRate) / 100;
        $totalPayable = $requestedAmount + $interestAmount;

        // Get duration range label
        $durationLabel = $selectedLoanType->getDurationRangeLabel($requestedAmount);

        $result = [
            'user'                => $selectedUser,
            'loan_type'           => $selectedLoanType,
            'financial_year'      => $financialYear,
            'total_savings'       => $totalSavings,
            'active_loan_balance' => $activeLoanBalance,
            'available_savings'   => $availableSavings,
            'loan_limit'          => $loanLimit,
            'requested_amount'    => $requestedAmount,
            'qualifies'           => $qualifies,
            'shortfall'           => $qualifies ? 0 : round($requestedAmount - $loanLimit, 2),
            'due_date'            => $dueDate,
            'duration_days'       => $durationDays,
            'duration_label'      => $durationLabel,
            'interest_rate'       => $interestRate,
            'interest_amount'     => $interestAmount,
            'total_payable'       => $totalPayable,
        ];

        return view('loans.eligibility', compact('members', 'result', 'loanTypes'));
    }

    /**
     * Apply for a loan - persists to database
     */
    public function apply(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount'  => 'required|numeric|min:1',
            'loan_type' => 'required|exists:loan_types,slug',
            'purpose' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $selectedUser = User::findOrFail($validated['user_id']);
        $selectedLoanType = LoanType::where('slug', $validated['loan_type'])->firstOrFail();

        // Verify eligibility again before saving
        $financialYear = Saving::getCurrentFinancialYear();
        $totalSavings = Saving::where('user_id', $selectedUser->id)
            ->where('financial_year', $financialYear)
            ->where('status', 'approved')
            ->sum('amount');

        $activeLoanBalance = Loan::where('user_id', $selectedUser->id)
            ->whereIn('status', ['approved', 'active'])
            ->get()
            ->sum(function ($loan) {
                return $loan->balance;
            });

        $availableSavings = max(0, $totalSavings - $activeLoanBalance);
        $loanLimit = round($availableSavings * 0.75, 2);
        $requestedAmount = (float) $validated['amount'];

        if ($requestedAmount > $loanLimit) {
            return back()->withInput()
                ->withErrors(['amount' => 'Amount exceeds your loan limit of KES ' . number_format($loanLimit, 2)]);
        }

        // Get duration
        $durationDays = $selectedLoanType->getDurationForAmount($requestedAmount);
        $dueDate = now()->addDays($durationDays);

        // Calculate interest
        $interestRate = $selectedLoanType->interest_rate;
        $interestAmount = ($requestedAmount * $interestRate) / 100;
        $totalPayable = $requestedAmount + $interestAmount;

        // Generate loan number
        $loanNumber = 'LN-' . date('Y') . '-' . strtoupper(Str::random(8));

        // Create loan record
        $loan = Loan::create([
            'user_id' => $selectedUser->id,
            'loan_number' => $loanNumber,
            'loan_type' => $selectedLoanType->slug,
            'amount_taken' => $requestedAmount,
            'amount_due' => $totalPayable,
            'interest_rate' => $interestRate,
            'interest_amount' => $interestAmount,
            'total_amount_payable' => $totalPayable,
            'date_taken' => now(),
            'date_due' => $dueDate,
            'payment_frequency' => 'monthly',
            'installment_amount' => $totalPayable / 1, // Single payment for now
            'installments_count' => 1,
            'installments_paid' => 0,
            'status' => 'pending',
            'purpose' => $validated['purpose'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan application submitted successfully! Loan #: ' . $loan->loan_number);
    }

    /**
     * Display a listing of all loans
     */
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('loan_number', 'LIKE', "%{$request->search}%")
                  ->orWhereHas('user', function($q2) use ($request) {
                      $q2->where('name', 'LIKE', "%{$request->search}%")
                         ->orWhere('member_id', 'LIKE', "%{$request->search}%");
                  });
            });
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get all users for filter dropdown
        $users = User::orderBy('name')->get();

        return view('loans.index', compact('loans', 'users'));
    }

    /**
     * Display a single loan
     */
    public function show(Loan $loan)
    {
        $loan->load(['user', 'approver', 'guarantors.user', 'payments']);
        return view('loans.show', compact('loan'));
    }

    /**
     * Approve a loan
     */
    public function approve(Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loans can be approved.');
        }

        $loan->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan approved successfully!');
    }

    /**
     * Activate a loan (disburse funds)
     */
    public function activate(Loan $loan)
    {
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Only approved loans can be activated.');
        }

        $loan->update([
            'status' => 'active',
            'date_taken' => now(),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan activated and funds disbursed!');
    }

    /**
     * Cancel a loan
     */
    public function cancel(Loan $loan)
    {
        if (!in_array($loan->status, ['pending', 'approved'])) {
            return back()->with('error', 'Only pending or approved loans can be cancelled.');
        }

        $loan->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan cancelled successfully!');
    }

    /**
     * Mark loan as completed
     */
    public function complete(Loan $loan)
    {
        if ($loan->status !== 'active') {
            return back()->with('error', 'Only active loans can be marked as completed.');
        }

        $loan->update([
            'status' => 'completed',
            'date_paid' => now(),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan marked as completed!');
    }

    public function instant()
    {
        return view('loans.instant');
    }

    public function ordinary()
    {
        return view('loans.ordinary');
    }
    public function pending(Request $request)
    {
        $loans = Loan::with(['user', 'approver'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $users = User::orderBy('name')->get();
        
        return view('loans.index', compact('loans', 'users'));
    }
    public function active(Request $request)
    {
        $loans = Loan::with(['user', 'approver'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $users = User::orderBy('name')->get();
        
        return view('loans.index', compact('loans', 'users'));
    }
    public function completed(Request $request)
    {
        $loans = Loan::with(['user', 'approver'])
            ->completed()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $users = User::orderBy('name')->get();
        
        return view('loans.index', compact('loans', 'users'));
    }
     public function overdue(Request $request)
    {
        $loans = Loan::with(['user', 'approver'])
            ->overdue()
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $users = User::orderBy('name')->get();
        
        return view('loans.index', compact('loans', 'users'));
    }
        public function defaulted(Request $request)
        {
            $loans = Loan::with(['user', 'approver'])
                ->where('status', 'defaulted')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            $users = User::orderBy('name')->get();
            
            return view('loans.index', compact('loans', 'users'));
        }

    public function getDurationTiers(Request $request)
    {
        $loanType = LoanType::where('slug', 'ordinary')->first();
        if (!$loanType) {
            return response()->json([]);
        }
        
        $tiers = $loanType->durations()->where('is_active', true)->orderBy('min_amount')->get();
        return response()->json($tiers);
    }
    public function byStatus($status)
    {
        $validStatuses = ['pending', 'approved', 'active', 'completed', 'defaulted', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            abort(404);
        }
        
        $loans = Loan::with(['user', 'approver'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $users = User::orderBy('name')->get();
        
        return view('loans.index', compact('loans', 'users'));
    }
}