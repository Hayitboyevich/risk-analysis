<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['id' => 1, 'name' => 'create_object'],
            ['id' => 2, 'name' => 'update_object'],
            ['id' => 3, 'name' => 'update_checklist'],
            ['id' => 4, 'name' => 'users_create'],
            ['id' => 5, 'name' => 'users_delete'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->create($permission);

        }

        RolePermission::query()->create([
            'permission_id' => 1,
            'role_id'       => UserRoleEnum::GASN_INSPECTOR->value,
        ]);

        RolePermission::query()->create([
            'permission_id' => 1,
            'role_id'       => UserRoleEnum::KVARTIRA_INSPECTOR->value,
        ]);

        RolePermission::query()->create([
            'permission_id' => 1,
            'role_id'       => UserRoleEnum::SUV_INSPECTOR->value,
        ]);

        RolePermission::query()->create([
            'permission_id' => 4,
            'role_id'       => UserRoleEnum::HTQ_KADR->value,
        ]);

        RolePermission::query()->create([
            'permission_id' => 4,
            'role_id'       => UserRoleEnum::ICHIMLIK_SUVI_KADR->value,
        ]);

        RolePermission::query()->create([
            'permission_id' => 4,
            'role_id'       => UserRoleEnum::SHAHARSOZLIK_KADR->value,
        ]);


    }
}
