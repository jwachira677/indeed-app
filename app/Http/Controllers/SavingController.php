<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SavingController extends Controller
{


    public function index(Request $request)
    {
        $financialYear = $request->get('financial_year', Saving::getCurrentFinancialYear());
        $selectedMonth = $request->get('month', date('n'));
        $selectedYear = $request->get('year', date('Y'));
        
        // Get all members who have saved
        $membersWithSavings = Saving::where('financial_year', $financialYear)
            ->where('status', 'approved')
            ->with('user')
            ->get()
            ->groupBy('user_id');

        // For specific month
        $savings = Saving::with(['user', 'approver'])
            ->when($request->filled('month'), function($query) use ($request) {
                return $query->where('month', $request->month);
            })
            ->when($request->filled('year'), function($query) use ($request) {
                return $query->where('year', $request->year);
            })
            ->when($request->filled('financial_year'), function($query) use ($request) {
                return $query->where('financial_year', $request->financial_year);
            })
            ->when($request->filled('status'), function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function($query) use ($request) {
                return $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'LIKE', "%{$request->search}%")
                      ->orWhere('member_id', 'LIKE', "%{$request->search}%");
                });
            })
            ->orderBy('saved_date', 'desc')
            ->paginate(20);

        // Get all financial years for dropdown
        $financialYears = Saving::distinct()->pluck('financial_year')->sort()->toArray();
        
        // Summary statistics
        $summary = $this->getSummary($financialYear);

        return view('savings.index', compact(
            'savings', 
            'financialYear', 
            'selectedMonth', 
            'selectedYear',
            'financialYears',
            'summary'
        ));
    }

    public function create()
    {
        $members = User::members()->where('status', 'active')->orderBy('name')->get();
        $financialYear = Saving::getCurrentFinancialYear();
        
        return view('savings.create', compact('members', 'financialYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mpesa,cheque,other',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $currentMonth = date('n');
        $currentYear = date('Y');
        $financialYear = Saving::getFinancialYear($currentYear, $currentMonth);

        // Get current balance
        $currentBalance = Saving::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $saving = Saving::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance + $validated['amount'],
            'month' => $currentMonth,
            'year' => $currentYear,
            'financial_year' => $financialYear,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'saved_date' => now(),
        ]);

        return redirect()->route('savings.index')
            ->with('success', 'Savings recorded successfully! Receipt #: ' . $saving->receipt_number);
    }

    public function show(Saving $saving)
    {
        return view('savings.show', compact('saving'));
    }

    public function edit(Saving $saving)
    {
        // Only allow editing if status is pending
        if ($saving->status !== 'pending') {
            return back()->with('error', 'Only pending savings can be edited.');
        }

        $members = User::members()->where('status', 'active')->orderBy('name')->get();
        return view('savings.edit', compact('saving', 'members'));
    }

    public function update(Request $request, Saving $saving)
    {
        if ($saving->status !== 'pending') {
            return back()->with('error', 'Only pending savings can be updated.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mpesa,cheque,other',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $saving->update($validated);

        return redirect()->route('savings.show', $saving)
            ->with('success', 'Savings updated successfully!');
    }

    public function destroy(Saving $saving)
    {
        if ($saving->status === 'approved') {
            return back()->with('error', 'Cannot delete approved savings.');
        }

        $saving->delete();

        return redirect()->route('savings.index')
            ->with('success', 'Savings deleted successfully!');
    }

    public function approve(Saving $saving)
    {
        if ($saving->status !== 'pending') {
            return back()->with('error', 'Only pending savings can be approved.');
        }

        // Update balance
        $currentBalance = Saving::where('user_id', $saving->user_id)
            ->where('status', 'approved')
            ->sum('amount');

        $saving->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance + $saving->amount,
        ]);

        return redirect()->route('savings.show', $saving)
            ->with('success', 'Savings approved successfully!');
    }

    public function reject(Saving $saving)
    {
        if ($saving->status !== 'pending') {
            return back()->with('error', 'Only pending savings can be rejected.');
        }

        $saving->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('savings.show', $saving)
            ->with('success', 'Savings rejected.');
    }

    public function memberSavings(User $member, Request $request)
    {
        $financialYear = $request->get('financial_year', Saving::getCurrentFinancialYear());
        
        $savings = Saving::where('user_id', $member->id)
            ->when($request->filled('financial_year'), function($query) use ($request) {
                return $query->where('financial_year', $request->financial_year);
            })
            ->orderBy('saved_date', 'desc')
            ->paginate(15);

        $summary = [
            'total_savings' => Saving::where('user_id', $member->id)->where('status', 'approved')->sum('amount'),
            'total_savings_year' => Saving::where('user_id', $member->id)
                ->where('financial_year', $financialYear)
                ->where('status', 'approved')
                ->sum('amount'),
            'total_entries' => $savings->total(),
            'pending_count' => Saving::where('user_id', $member->id)->where('status', 'pending')->count(),
            'financial_year' => $financialYear,
            'member' => $member,
        ];

        return view('savings.member', compact('savings', 'summary'));
    }

    public function summary()
    {
        $financialYear = Saving::getCurrentFinancialYear();
        $summary = $this->getSummary($financialYear);
        
        return response()->json($summary);
    }

    public function export(Request $request)
    {
        $savings = Saving::with(['user'])
            ->when($request->filled('financial_year'), function($query) use ($request) {
                return $query->where('financial_year', $request->financial_year);
            })
            ->where('status', 'approved')
            ->orderBy('saved_date', 'desc')
            ->get();

        $headers = [
            'Receipt #',
            'Member Name',
            'Member ID',
            'Amount',
            'Month',
            'Year',
            'Financial Year',
            'Payment Method',
            'Date',
            'Status',
        ];

        $data = $savings->map(function($saving) {
            return [
                $saving->receipt_number,
                $saving->user->name,
                $saving->user->member_id ?? 'N/A',
                number_format($saving->amount, 2),
                $saving->month_name,
                $saving->year,
                $saving->financial_year,
                ucfirst(str_replace('_', ' ', $saving->payment_method)),
                $saving->saved_date->format('Y-m-d'),
                ucfirst($saving->status),
            ];
        });

        return response()->stream(function() use ($headers, $data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="savings_export_' . date('Y-m-d') . '.csv"',
        ]);
    }

private function getSummary(string $financialYear): array
{
    $approvedRecords = Saving::where('financial_year', $financialYear)
        ->where('status', 'approved')
        ->orderBy('year')
        ->orderBy('month')
        ->get(['user_id', 'month', 'year', 'amount', 'total_savings_to_date']);

    // Latest record per user = their current running balance
    // (includes opening_balance + every deposit up to that point)
    $latestPerUser = $approvedRecords->groupBy('user_id')->map->last();

    $totalSavings = $latestPerUser->sum('total_savings_to_date');
    $totalMembers = $latestPerUser->count();

    return [
        'total_savings'       => $totalSavings,
        'total_entries'       => $approvedRecords->count(),
        'total_members'       => $totalMembers,
        'average_per_member'  => $totalMembers > 0 ? round($totalSavings / $totalMembers, 2) : 0,
        'months_with_savings' => $approvedRecords->pluck('month')->unique()->count(),
        'pending_count'       => Saving::where('financial_year', $financialYear)
            ->where('status', 'pending')
            ->count(),
    ];
}
}