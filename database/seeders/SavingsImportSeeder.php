<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SavingsImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Financial year: Dec 2025 -> Nov 2026.
     * "Month 0" in the sheet = opening_balance (not a deposit).
     * Only months with an actual value in the sheet are imported;
     * blank cells (months not yet reached) are skipped.
     * php artisan db:seed --class=SavingsImportSeeder
     */
    public function run(): void
    {
        $financialYear = '2025/2026';

        // month_key => [calendar month number, calendar year]
        $monthMap = [
            'Dec'   => [12, 2025],
            'Jan'   => [1, 2026],
            'Feb'   => [2, 2026],
            'March' => [3, 2026],
            'April' => [4, 2026],
            'May'   => [5, 2026],
        ];

        $data = [
            'John Wachira'  => ['opening' => 12722, 'months' => ['Dec' => 5000,  'Jan' => 6000, 'Feb' => 7000, 'March' => 8000, 'April' => 9000, 'May' => 10000]],
            'Ken Kagori'    => ['opening' => 9170,  'months' => ['Dec' => 15000, 'Jan' => 5000, 'Feb' => 5000, 'March' => 15000,'April' => 2000, 'May' => 5000]],
            'Mercy Karanja' => ['opening' => 23726, 'months' => ['Dec' => 28000, 'Jan' => 0,    'Feb' => 0,    'March' => 0,    'April' => 0,    'May' => 0]],
            'Milkah Kamotho'=> ['opening' => 13868, 'months' => ['Dec' => 4000,  'Jan' => 5000, 'Feb' => 5000, 'March' => 5000, 'April' => 5000, 'May' => 5000]],
            'Sarah Karanja' => ['opening' => 14625, 'months' => ['Dec' => 5000,  'Jan' => 5000, 'Feb' => 4000, 'March' => 4000, 'April' => 5000]], // May not yet recorded
            'Wyclif Muchai' => ['opening' => 0,     'months' => ['Dec' => 2000,  'Jan' => 5000, 'Feb' => 5000, 'March' => 5000, 'April' => 5000, 'May' => 3000]],
            'Martin Gitau'  => ['opening' => 7122,  'months' => ['Dec' => 2500,  'Jan' => 2500, 'Feb' => 2000, 'March' => 2000, 'April' => 2500, 'May' => 4000]],
            'Grace Kamotho' => ['opening' => 0,     'months' => ['Dec' => 3000,  'Jan' => 3000, 'Feb' => 3000, 'March' => 3000, 'April' => 1500, 'May' => 3000]],
            'Naomi Nduta'   => ['opening' => 0,     'months' => ['Dec' => 1000,  'Jan' => 1000, 'Feb' => 1000, 'March' => 1000, 'April' => 2500, 'May' => 10000]],
            'Naomi Karanja' => ['opening' => 0,     'months' => ['Dec' => 8000,  'Jan' => 1000, 'Feb' => 2000, 'March' => 1000, 'April' => 1000, 'May' => 2000]],
            'Eva Wambui'    => ['opening' => 4023,  'months' => ['Dec' => 1000,  'Jan' => 1000, 'Feb' => 1000, 'March' => 1000, 'April' => 1000, 'May' => 1000]],
            'Geoffrey Kiiru'=> ['opening' => 0,     'months' => ['Dec' => 1500,  'Jan' => 1000, 'Feb' => 1000, 'March' => 1500, 'April' => 1000, 'May' => 1000]],
            'Jane Muthoni'  => ['opening' => 887,   'months' => ['Dec' => 1000,  'Jan' => 1000, 'Feb' => 1000, 'March' => 1000, 'April' => 1000, 'May' => 1000]],
            'Evalyne Cimbi' => ['opening' => 0,     'months' => ['Dec' => 1000,  'Jan' => 1000, 'Feb' => 1000, 'March' => 1000, 'April' => 1000, 'May' => 1000]],
            'Phoeby Wanjiku'=> ['opening' => 0,     'months' => ['Dec' => 0,     'Jan' => 0,    'Feb' => 0,    'March' => 0,    'April' => 0,    'May' => 0]],
        ];

        foreach ($data as $name => $record) {
            $userId = DB::table('users')->where('name', $name)->value('id');

            if (! $userId) {
                $this->command?->warn("Skipping \"{$name}\" - no matching user found in users table.");
                continue;
            }

            $openingBalance = $record['opening'];
            $runningBalance = $openingBalance;

            foreach ($record['months'] as $monthKey => $amount) {
                [$monthNum, $year] = $monthMap[$monthKey];

                $balanceBefore = $runningBalance;
                $balanceAfter  = $runningBalance + $amount;
                $runningBalance = $balanceAfter;

                DB::table('savings')->insert([
                    'user_id'                => $userId,
                    'approved_by'             => null,
                    'receipt_number'          => sprintf('SAV-%d-%02d%d', $userId, $monthNum, $year),
                    'amount'                  => $amount,
                    'opening_balance'         => $openingBalance,
                    'is_carried_forward'      => 0,
                    'carried_forward_amount'  => 0.00,
                    'total_savings_to_date'   => $balanceAfter,
                    'balance_before'          => $balanceBefore,
                    'balance_after'           => $balanceAfter,
                    'month'                   => $monthNum,
                    'year'                    => $year,
                    'financial_year'          => $financialYear,
                    'payment_method'          => 'cash',
                    'transaction_id'          => null,
                    'notes'                   => 'Imported from legacy savings record',
                    'status'                  => 'approved',
                    'saved_date'              => Carbon::create($year, $monthNum, 1),
                    'approved_at'             => Carbon::create($year, $monthNum, 1),
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);
            }
        }
    }
}