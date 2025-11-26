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
            'name' => 'Xavf',
            'surname' => 'Taxlil',
            'pin' => '5457545154545154545',
            'phone' => '998771111111',
            'password' => bcrypt('^fA7U4Z6==UA'),
            'login' => 'xtq_admin',
            'active' => 1,
            'user_status_id' => 1,
            'type' => 1,
            'region_id' => 1,
            'district_id' => 1,
        ]);

        $user->roles()->sync([31,32,33,37,39,40,41]);
    }
}
