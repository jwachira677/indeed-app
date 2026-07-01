<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FineController extends Controller
{
    public function index(Request $request)
    {
        $fines = Fine::with(['user', 'approver'])
            ->when($request->filled('status'), function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('user_id'), function($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->filled('search'), function($query) use ($request) {
                return $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'LIKE', "%{$request->search}%")
                      ->orWhere('member_id', 'LIKE', "%{$request->search}%");
                })->orWhere('fine_number', 'LIKE', "%{$request->search}%");
            })
            ->when($request->filled('date_from'), function($query) use ($request) {
                return $query->whereDate('issued_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                return $query->whereDate('issued_date', '<=', $request->date_to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Summary statistics
        $summary = [
            'total_fines' => Fine::whereIn('status', ['paid', 'partially_paid', 'overdue'])->sum('amount'),
            'total_paid' => Fine::where('status', 'paid')->sum('amount'),
            'total_unpaid' => Fine::whereIn('status', ['pending', 'overdue'])->sum('balance'),
            'total_overdue' => Fine::overdue()->sum('balance'),
            'total_fines_count' => Fine::count(),
            'pending_count' => Fine::pending()->count(),
            'overdue_count' => Fine::overdue()->count(),
        ];

        $members = User::members()->where('status', 'active')->orderBy('name')->get();

        return view('fines.index', compact('fines', 'summary', 'members'));
    }

    public function create()
    {
        $members = User::members()->where('status', 'active')->orderBy('name')->get();
        return view('fines.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        $fine = Fine::create([
            'user_id' => $validated['user_id'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => 'pending',
            'balance' => $validated['amount'],
            'issued_date' => now(),
        ]);

        return redirect()->route('fines.show', $fine)
            ->with('success', 'Fine issued successfully! Fine #: ' . $fine->fine_number);
    }

    public function show(Fine $fine)
    {
        $fine->load(['user', 'approver']);
        return view('fines.show', compact('fine'));
    }

    public function edit(Fine $fine)
    {
        // Only allow editing if not paid or waived
        if (in_array($fine->status, ['paid', 'waived'])) {
            return back()->with('error', 'Cannot edit a paid or waived fine.');
        }

        $members = User::members()->where('status', 'active')->orderBy('name')->get();
        return view('fines.edit', compact('fine', 'members'));
    }

    public function update(Request $request, Fine $fine)
    {
        if (in_array($fine->status, ['paid', 'waived'])) {
            return back()->with('error', 'Cannot update a paid or waived fine.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'status' => 'nullable|in:pending,overdue',
        ]);

        $fine->update($validated);

        return redirect()->route('fines.show', $fine)
            ->with('success', 'Fine updated successfully!');
    }

    public function destroy(Fine $fine)
    {
        if (in_array($fine->status, ['paid', 'waived'])) {
            return back()->with('error', 'Cannot delete a paid or waived fine.');
        }

        $fine->delete();

        return redirect()->route('fines.index')
            ->with('success', 'Fine deleted successfully!');
    }

    public function pay(Request $request, Fine $fine)
    {
        if (in_array($fine->status, ['paid', 'waived'])) {
            return back()->with('error', 'This fine is already paid or waived.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $fine->balance,
            'payment_method' => 'required|in:cash,bank_transfer,mpesa,cheque,other',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $fine->recordPayment(
            $validated['amount'],
            $validated['payment_method'],
            $validated['transaction_id'] ?? null
        );

        $message = $fine->status === 'paid' 
            ? 'Fine paid in full! Thank you.' 
            : 'Partial payment recorded. Remaining balance: KES ' . number_format($fine->balance, 2);

        return redirect()->route('fines.show', $fine)
            ->with('success', $message);
    }

    public function waive(Fine $fine)
    {
        if (in_array($fine->status, ['paid', 'waived'])) {
            return back()->with('error', 'This fine is already paid or waived.');
        }

        $fine->waive(Auth::id());

        return redirect()->route('fines.show', $fine)
            ->with('success', 'Fine waived successfully!');
    }

    public function memberFines(User $member, Request $request)
    {
        $fines = Fine::where('user_id', $member->id)
            ->when($request->filled('status'), function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $summary = [
            'total_fines' => Fine::where('user_id', $member->id)
                ->whereIn('status', ['paid', 'partially_paid', 'overdue'])
                ->sum('amount'),
            'total_paid' => Fine::where('user_id', $member->id)
                ->where('status', 'paid')
                ->sum('amount'),
            'total_unpaid' => Fine::where('user_id', $member->id)
                ->whereIn('status', ['pending', 'overdue'])
                ->sum('balance'),
            'total_overdue' => Fine::where('user_id', $member->id)
                ->overdue()
                ->sum('balance'),
            'pending_count' => Fine::where('user_id', $member->id)
                ->pending()
                ->count(),
            'overdue_count' => Fine::where('user_id', $member->id)
                ->overdue()
                ->count(),
        ];

        return view('fines.member', compact('fines', 'summary', 'member'));
    }

    public function export(Request $request)
    {
        $fines = Fine::with(['user'])
            ->when($request->filled('status'), function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function($query) use ($request) {
                return $query->whereDate('issued_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                return $query->whereDate('issued_date', '<=', $request->date_to);
            })
            ->orderBy('issued_date', 'desc')
            ->get();

        $headers = [
            'Fine #',
            'Member Name',
            'Member ID',
            'Amount',
            'Paid',
            'Balance',
            'Reason',
            'Status',
            'Due Date',
            'Date Paid',
            'Issued Date',
        ];

        $data = $fines->map(function($fine) {
            return [
                $fine->fine_number,
                $fine->user->name,
                $fine->user->member_id ?? 'N/A',
                number_format($fine->amount, 2),
                number_format($fine->amount_paid, 2),
                number_format($fine->balance, 2),
                $fine->reason,
                ucfirst($fine->status),
                $fine->due_date ? $fine->due_date->format('Y-m-d') : 'N/A',
                $fine->date_paid ? $fine->date_paid->format('Y-m-d') : 'Not paid',
                $fine->issued_date->format('Y-m-d'),
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
            'Content-Disposition' => 'attachment; filename="fines_export_' . date('Y-m-d') . '.csv"',
        ]);
    }
}