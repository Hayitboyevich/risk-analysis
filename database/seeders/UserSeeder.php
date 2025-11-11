<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Shahzod',
            'surname' => 'Ruziev',
            'middle_name' => 'Hayitboyevich',
            'region_id' => 1,
            'district_id' => 1,
            'pin' => '31703975270028',
            'active' => true,
            'user_status_id' => 1,
            'login' => '123',
            'phone' => '998337071727',
            'password' => Hash::make('123456'),
        ]);
        UserRole::query()->create([
            'user_id' => 1,
            'role_id' => 31,
        ]);

        User::query()->create([
            'name' => 'Obidjon',
            'surname' => 'Marufjonov',
            'middle_name' => '',
            'region_id' => 1,
            'district_id' => 1,
            'pin' => '52809005070031',
            'active' => true,
            'user_status_id' => 1,
            'login' => '1234',
            'phone' => '5748948648648',
            'password' => Hash::make('123456'),
        ]);
        UserRole::query()->create([
            'user_id' => 2,
            'role_id' => 31,
        ]);

        UserRole::query()->create([
            'user_id' => 2,
            'role_id' => 32,
        ]);

        UserRole::query()->create([
            'user_id' => 2,
            'role_id' => 33,
        ]);
        UserRole::query()->create([
            'user_id' => 2,
            'role_id' => 37,
        ]);

        UserRole::query()->create([
            'user_id' => 2,
            'role_id' => 39,
        ]);

        UserRole::query()->create([
            'user_id' => 2,
            'role_id' => 40,
        ]);

        UserRole::query()->create([
            'user_id' => 2,
            'role_id' => 41,
        ]);

    }
}
