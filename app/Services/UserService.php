<?php

namespace App\Services;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserRole;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function __construct(public User $user, public Client $client)
    {
    }

    public function getAllUsers($user, $roleId, $type = null)
    {
        switch ($roleId) {
            case UserRoleEnum::HTQ_KADR->value:
                return $this->user->whereHas('roles', function ($query) {
                    $query->whereIn('role_id', [
                        UserRoleEnum::KVARTIRA_INSPECTOR->value,
                        UserRoleEnum::GASN_INSPECTOR->value,
                        UserRoleEnum::SUV_INSPECTOR->value
                    ]);
                });

            case UserRoleEnum::SHAHARSOZLIK_KADR->value:
                return $this->user->whereHas('roles', function ($query) {
                    $query->whereIn('role_id', [
                        UserRoleEnum::GASN_INSPECTOR->value,
                    ]);
                });

            case UserRoleEnum::ICHIMLIK_SUVI_KADR->value:
            case UserRoleEnum::RES_KUZATUVCHI->value:
                return $this->user->whereHas('roles', function ($query) {
                    $query->whereIn('role_id', [
                        UserRoleEnum::SUV_INSPECTOR->value,
                    ]);
                });

            default:
                return $this->user->query()->whereRaw('1 = 0');
        }
    }

    public function searchByUser($query, $filters)
    {
        return $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->searchAll($filters['search']);
        })
            ->when(isset($filters['region_id']), function ($q) use ($filters) {
                $q->where('region_id', $filters['region_id']);
            })
            ->when(isset($filters['district_id']), function ($q) use ($filters) {
                $q->where('district_id', $filters['district_id']);
            })
            ->when(isset($filters['status']), function ($q) use ($filters) {
                $q->where('user_status_id', $filters['status']);
            })
            ->when(isset($filters['role_id']), function ($q) use ($filters) {
                $q->whereHas('roles', function ($q) use ($filters) {
                    $q->where('role_id', $filters['role_id']);
                });
            });
    }

    public function findByPin($pin)
    {
        $user = $this->user->query()->where('pin', $pin)->first();
        if (!$user) {
            return null;
        }
        return $user;
    }

    public function createNewUser(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->pin = $request->pinfl;
            $user->password = Hash::make($request->phone);
            $user->login = $request->phone;
            $user->user_status_id = $request->user_status_id;
            $user->surname = $request->surname;
            $user->middle_name = $request->middle_name;
            $user->region_id = $request->region_id;
            $user->district_id = $request->district_id;
            $user->created_by = $request->created_by;
            $user->type = $request->type;
            $user->image = $this->saveImage($request);
            $user->save();

            $this->updateUserRoles($user, $request->role_ids);
            $this->saveFiles($user, $request);
            DB::commit();
            return $user;

        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function updateUser($user, UserRequest $request)
    {
       DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->surname = $request->surname;
            $user->middle_name = $request->middle_name;
            $user->region_id = $request->region_id;
            $user->district_id = $request->district_id;
            $user->active = 1;
            $user->login = $request->phone;
            $user->password = Hash::make($request->phone);
            $user->user_status_id = UserStatusEnum::ACTIVE;
            $user->type = $request->type;
            $user->created_by = $request->created_by;
            $user->image = $this->saveImage($request);
            $user->save();

            $this->updateUserRoles($user, $request->role_ids);
            $this->saveFiles($user, $request);
            DB::commit();
            return $user;
        }catch (\Exception $exception){
            DB::rollBack();
            throw $exception;
        }
    }

    private function saveImage(UserRequest $request): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('user', 'public');
        }

        return null;
    }

    private function updateUserRoles($user, array $roleIds): void
    {
        $existingRoles = UserRole::query()
            ->where('user_id', $user->id)
            ->pluck('role_id')
            ->toArray();

        $rolesToAdd = array_diff($roleIds, $existingRoles);

        $rolesToRemove = array_diff($existingRoles, $roleIds);

        if (!empty($rolesToRemove)) {
            UserRole::query()
                ->where('user_id', $user->id)
                ->whereIn('role_id', $rolesToRemove)
                ->delete();
        }

        foreach ($rolesToAdd as $role_id) {
            UserRole::query()->create([
                'user_id' => $user->id,
                'role_id' => $role_id
            ]);
        }
    }

    private function saveFiles($user, UserRequest $request)
    {
        if ($request->hasFile('files')) {
            $user->documents()->delete();
            foreach ($request->file('files') as $file) {
                $path = $file->store('user/docs', 'public');
                $user->documents()->create(['url' => $path]);
            }
        }
    }

    public function getInfo(string $pinfl, string $birth_date)
    {
        try {
            $resClient = $this->client->post(config('app.passport.url') . '?pinfl=' . $pinfl . '&birth_date=' . $birth_date,
                [
                    'headers' => [
                        'Authorization' => 'Basic ' . base64_encode(config('app.passport.login') . ':' . config('app.passport.password')),
                    ],
                    'verify' => false,
                ]);
            $response = json_decode($resClient->getBody(), true);

            return $response['result']['data']['data'][0];

        } catch (BadResponseException $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

}
