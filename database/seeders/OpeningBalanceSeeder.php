<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OpeningBalanceSeeder extends Seeder
{
    /**
     * Backfills a "Month 0" savings row per member representing the balance
     * carried forward from the previous savings cycle. Safe to re-run —
     * skips any member who already has a Month 0 record.
     * php artisan db:seed --class=OpeningBalanceSeeder
     */
    public function run(): void
    {
        $financialYear = '2025/2026';
        $year  = 2025; // the cycle Month 0 feeds into (Dec 2025 start)
        $month = 0;

        // saved_date for the carried-forward entry: end of the prior cycle
        $savedDate = Carbon::create($year, 12, 1)->subDay(); // Nov 30, 2025

        $openingBalances = [
            'John Wachira'   => 12722,
            'Ken Kagori'     => 9170,
            'Mercy Karanja'  => 23726,
            'Milkah Kamotho' => 13868,
            'Sarah Karanja'  => 14625,
            'Wyclif Muchai'  => 0,
            'Martin Gitau'   => 7122,
            'Grace Kamotho'  => 0,
            'Naomi Nduta'    => 0,
            'Naomi Karanja'  => 0,
            'Eva Wambui'     => 4023,
            'Geoffrey Kiiru' => 0,
            'Jane Muthoni'   => 887,
            'Evalyne Cimbi'  => 0,
            'Phoeby Wanjiku' => 0,
        ];

        foreach ($openingBalances as $name => $opening) {
            $userId = DB::table('users')->where('name', $name)->value('id');

            if (! $userId) {
                $this->command?->warn("Skipping \"{$name}\" - no matching user found.");
                continue;
            }

            $receiptNumber = sprintf('SAV-%d-%02d%d', $userId, $month, $year);

            // Idempotent: don't duplicate if this seeder has already run.
            if (DB::table('savings')->where('receipt_number', $receiptNumber)->exists()) {
                continue;
            }

            DB::table('savings')->insert([
                'user_id'                => $userId,
                'approved_by'             => null,
                'receipt_number'          => $receiptNumber,
                'amount'                  => $opening,
                'opening_balance'         => $opening,
                'is_carried_forward'      => 1,
                'carried_forward_amount'  => $opening,
                'total_savings_to_date'   => $opening,
                'balance_before'          => 0,
                'balance_after'           => $opening,
                'month'                   => $month,
                'year'                    => $year,
                'financial_year'          => $financialYear,
                'payment_method'          => 'cash',
                'transaction_id'          => null,
                'notes'                   => 'Opening balance carried forward from previous savings cycle',
                'status'                  => 'approved',
                'saved_date'              => $savedDate,
                'approved_at'             => $savedDate,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);
        }
    }
}