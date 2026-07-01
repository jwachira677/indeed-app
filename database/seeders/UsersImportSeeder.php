<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
//php artisan db:seed --class=UsersImportSeeder
class UsersImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('1234567');
        $now = now();

        $users = [
            ['role_id' => 1, 'name' => 'John Wachira',  'phone_number' => '705', 'status' => 'active'],
            ['role_id' => 2, 'name' => 'Ken Kagori',     'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Mercy Karanja',  'phone_number' => null,  'status' => 'inactive'], // Halted April
            ['role_id' => 2, 'name' => 'Milkah Kamotho', 'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Sarah Karanja',  'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Wyclif Muchai',  'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Martin Gitau',   'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Grace Kamotho',  'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Naomi Nduta',    'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Naomi Karanja',  'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Eva Wambui',     'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Geoffrey Kiiru', 'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Jane Muthoni',   'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Evalyne Cimbi',  'phone_number' => null,  'status' => 'active'],
            ['role_id' => 2, 'name' => 'Phoeby Wanjiku', 'phone_number' => null,  'status' => 'inactive'], // Halted Feb
        ];

        foreach ($users as $user) {
            [$first, $last] = array_pad(explode(' ', $user['name'], 2), 2, '');
            $email = strtolower($last . $first) . '@friendsindeed.com';

            DB::table('users')->insert([
                'role_id'      => $user['role_id'],
                'name'         => $user['name'],
                'email'        => $email,
                'phone_number' => $user['phone_number'],
                'status'       => $user['status'],
                'password'     => $password,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}