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
        $user = User::query()->create([
            'name' => 'Ali',
            'surname' => 'Valiyev',
            'pin' => '84848484548154',
            'phone' => '998772222222',
//            'password' => bcrypt('^fA7U4Z6==UA'),
            'password' => bcrypt(')JF39i8aj4a6'),
            'login' => 'xtq_admin2',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user->roles()->sync([31,32,33,37,39,40,41]);
    }
}
