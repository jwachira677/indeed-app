<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use App\Models\User;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function eligibilityForm()
    {
        $members = User::orderBy('name')->get();
        return view('loans.eligibility', compact('members'));
    }

public function checkEligibility(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'amount'  => 'required|numeric|min:1',
    ]);

    $members = User::orderBy('name')->get();
    $selectedUser = User::findOrFail($validated['user_id']);

    $financialYear = Saving::getCurrentFinancialYear();

    // Get total savings
    $totalSavings = Saving::where('user_id', $selectedUser->id)
        ->where('financial_year', $financialYear)
        ->where('status', 'approved')
        ->sum('amount');

    // Get active loan balance (total outstanding on active loans)
    $activeLoanBalance = Loan::where('user_id', $selectedUser->id)
        ->whereIn('status', ['approved', 'active'])
        ->get()
        ->sum(function ($loan) {
            return $loan->balance; // Use the balance accessor
        });

    // Calculate available savings (savings - active loan balance)
    $availableSavings = max(0, $totalSavings - $activeLoanBalance);
    
    // Calculate loan limit (75% of available savings)
    $loanLimit = round($availableSavings * 0.75, 2);
    
    $requestedAmount = (float) $validated['amount'];
    $qualifies = $requestedAmount <= $loanLimit;

    $result = [
        'user'                => $selectedUser,
        'financial_year'      => $financialYear,
        'total_savings'       => $totalSavings,
        'active_loan_balance' => $activeLoanBalance,
        'available_savings'   => $availableSavings,
        'loan_limit'          => $loanLimit,
        'requested_amount'    => $requestedAmount,
        'qualifies'           => $qualifies,
        'shortfall'           => $qualifies ? 0 : round($requestedAmount - $loanLimit, 2),
    ];

    return view('loans.eligibility', compact('members', 'result'));
}

    public function instant()
    {
        return view('loans.instant');
    }

    public function ordinary()
    {
        return view('loans.ordinary');
    }
}