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
    }
}
