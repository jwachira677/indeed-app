<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $year2025 = DB::table('academic_years')->where('year_name', '2025')->value('id');
        $term1    = DB::table('terms')->where('academic_year_id', $year2025)->where('term_number', 1)->value('id');
        $mealsId  = DB::table('fee_types')->where('code', 'MEALS')->value('id');

        // 1. Generate bills for all active students (compulsory fees, term 1 2025)
        DB::table('student_enrollments')
            ->where('status', 'ACTIVE')
            ->get()
            ->each(function ($se) use ($term1) {
                $structures = DB::table('class_fee_structures')
                    ->where('class_id', $se->class_id)
                    ->where('term_id', $term1)
                    ->where('is_optional', false)
                    ->get();

                foreach ($structures as $s) {
                    DB::table('student_fee_bills')->insert([
                        'student_enrollment_id'  => $se->id,
                        'class_fee_structure_id' => $s->id,
                        'term_id'                => $term1,
                        'amount_due'             => $s->amount,
                        'discount'               => 0,
                        'status'                 => 'PENDING',
                        'due_date'               => $s->due_date,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            });

        // 2. Some students opt into MEALS
        $mealOptInEnrollments = DB::table('student_enrollments')
            ->whereIn('student_id', [1, 2, 5, 8])   // first 2 of G4, one of G7, one of G10
            ->pluck('id');

        foreach ($mealOptInEnrollments as $eid) {
            $se = DB::table('student_enrollments')->find($eid);
            $mealStructure = DB::table('class_fee_structures')
                ->where('class_id', $se->class_id)
                ->where('term_id', $term1)
                ->where('fee_type_id', $mealsId)
                ->first();

            if ($mealStructure) {
                DB::table('student_fee_bills')->insert([
                    'student_enrollment_id'  => $eid,
                    'class_fee_structure_id' => $mealStructure->id,
                    'term_id'                => $term1,
                    'amount_due'             => $mealStructure->amount,
                    'discount'               => 0,
                    'status'                 => 'PENDING',
                    'due_date'               => $mealStructure->due_date,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        // 3. Record a few sample payments + allocate FIFO to bills
        $payments = [
            ['student_id' => 1, 'amount' => 20000.00, 'method' => 'MPESA', 'ref' => 'QK45RTY9L', 'date' => '2025-02-05'],
            ['student_id' => 2, 'amount' =>  5000.00, 'method' => 'CASH',  'ref' => null,        'date' => '2025-02-06'],
            ['student_id' => 5, 'amount' => 26000.00, 'method' => 'BANK',  'ref' => 'KCB-88771', 'date' => '2025-02-07'],
            ['student_id' => 8, 'amount' => 10000.00, 'method' => 'MPESA', 'ref' => 'TK91PLK2M', 'date' => '2025-02-08'],
        ];

        foreach ($payments as $p) {
            DB::transaction(function () use ($p, $term1) {
                $paymentId = DB::table('payments')->insertGetId([
                    'student_id'       => $p['student_id'],
                    'payment_date'     => $p['date'],
                    'amount'           => $p['amount'],
                    'payment_method'   => $p['method'],
                    'reference_number' => $p['ref'],
                    'received_by'      => null,
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                $remaining = $p['amount'];

                $bills = DB::table('student_fee_bills as b')
                    ->join('student_enrollments as se', 'se.id', '=', 'b.student_enrollment_id')
                    ->where('se.student_id', $p['student_id'])
                    ->where('b.term_id', $term1)
                    ->whereIn('b.status', ['PENDING', 'PARTIAL'])
                    ->orderBy('b.due_date')
                    ->orderBy('b.id')
                    ->select('b.*')
                    ->get();

                foreach ($bills as $bill) {
                    if ($remaining <= 0) break;

                    $paid = (float) DB::table('payment_allocations')
                        ->where('student_fee_bill_id', $bill->id)->sum('amount');
                    $balance = $bill->amount_due - $bill->discount - $paid;
                    if ($balance <= 0) continue;

                    $apply = min($remaining, $balance);

                    DB::table('payment_allocations')->insert([
                        'payment_id'           => $paymentId,
                        'student_fee_bill_id'  => $bill->id,
                        'amount'               => $apply,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);

                    $remaining -= $apply;

                    $newPaid = $paid + $apply;
                    $status = ($newPaid >= ($bill->amount_due - $bill->discount))
                        ? 'PAID'
                        : ($newPaid > 0 ? 'PARTIAL' : 'PENDING');

                    DB::table('student_fee_bills')->where('id', $bill->id)
                        ->update(['status' => $status, 'updated_at' => now()]);
                }
            });
        }
    }
}