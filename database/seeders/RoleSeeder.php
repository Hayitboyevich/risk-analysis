<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->create([
            'id' => 31,
            'name' => 'Koʼp kvartirali uylarni boshqarishdagi xavflar',
            'description' => 'Koʼp kvartirali uylarni boshqarishdagi xavflar',
            'status' => true,
        ]);

        Role::query()->create([
            'id' => 32,
            'name' => 'Shaharsozlik faoliyati huquqbuzarliklari',
            'description' => 'Shaharsozlik faoliyati huquqbuzarliklari',
            'status' => true,
        ]);

        Role::query()->create([
            'id' => 33,
            'name' => 'Ichimlik suv taʼminoti va oqova suvlarning xavflari',
            'description' => 'Ichimlik suv taʼminoti va oqova suvlarning xavflari',
            'status' => true,
        ]);

        Role::query()->create([
            'id' => 37,
            'name' => 'Ko\'p kvartirali kadr',
            'description' => 'Ko\'p kvartirali kadr',
            'status' => true,
        ]);

        Role::query()->create([
            'id' => 39,
            'name' => 'Shaharsozlik kadr',
            'description' => 'Shaharsozlik kadr',
            'status' => true,
        ]);

        Role::query()->create([
            'id' => 40,
            'name' => 'Ichimlik suvi kadr',
            'description' => 'Ichimlik suvi kadr',
            'status' => true,
        ]);

        Role::query()->create([
            'id' => 41,
            'name' => 'Respublika Kuzatuvchi',
            'description' => 'Respublika Kuzatuvchi',
            'status' => true,
        ]);


    }
}
