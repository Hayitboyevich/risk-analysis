<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SuperAdmin extends Command
{
    protected $signature = 'app:super-admin';


    protected $description = 'Command description';

    public function handle()
    {
        $user1 = User::query()->create([
            'name' => 'Ali',
            'surname' => 'Valiyev',
            'pin' => '84848484548154',
            'phone' => '998772222222',
            'password' => bcrypt(')JF39i8aj4a6'),
            'login' => 'kvar_kadr_1',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user1->roles()->sync([37]);

        $user2 = User::query()->create([
            'name' => 'Vali',
            'surname' => 'Aliyev',
            'pin' => '84848484548154',
            'phone' => '998772222222',
            'password' => bcrypt('dD4R9£__K,59'),
            'login' => 'kvar_kadr_2',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user2->roles()->sync([37]);

        $user3 = User::query()->create([
            'name' => 'Bali',
            'surname' => 'Bohodirov',
            'pin' => '84848484548154',
            'phone' => '998772222222',
            'password' => bcrypt(':6xzKA0m%v13'),
            'login' => 'suv_kadr_1',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user3->roles()->sync([40]);

        $user4 = User::query()->create([
            'name' => 'Boho',
            'surname' => 'Qudratov',
            'pin' => '84848484548154',
            'phone' => '998772222222',
            'password' => bcrypt('tR8-)>920wsN'),
            'login' => 'suv_kadr_2',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user4->roles()->sync([40]);

        $user5 = User::query()->create([
            'name' => 'Xudoyberdi',
            'surname' => 'Allaberganov',
            'pin' => '84848484548154',
            'phone' => '998772222222',
            'password' => bcrypt('^QRn/u37f3g6'),
            'login' => 'shahar_kadr_1',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user5->roles()->sync([39]);

        $user6 = User::query()->create([
            'name' => 'Toshmat',
            'surname' => 'Berdiyev',
            'pin' => '84848484548154',
            'phone' => '998772222222',
            'password' => bcrypt('kLt419.YEu<p'),
            'login' => 'shahar_kadr_2',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user6->roles()->sync([39]);

        $user7 = User::query()->create([
            'name' => 'Qulmat',
            'surname' => 'Shermatov',
            'pin' => '84848484548154',
            'phone' => '998772222222',
            'password' => bcrypt('suZ!Qd6746p$'),
            'login' => 'res_kuzatuvchi_1',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user7->roles()->sync([41]);

        $user8 = User::query()->create([
            'name' => 'Shaxzod',
            'surname' => 'Eshmurodov',
            'pin' => '84848484548154',
            'phone' => '998772222222',
            'password' => bcrypt('Z0(5"H7)Xoh"'),
            'login' => 'res_kuzatuvchi_2',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user8->roles()->sync([41]);

    }
}
