<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DistrictResource;
use App\Http\Resources\RegionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserStatusResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends BaseController
{
    public function login(Request $request): JsonResponse
    {
        if ($request->has('pkcs7')) {
            $pkcs7 = $request->pkcs7;
            $data = $this->imzoService->getUserInfo($pkcs7);
            if (is_string($data)) return response()->json([
                'success' => false,
                'message' => $data
            ], 403);
            $identification_number = $data['identification_number'] ?? false;
            if (!$identification_number) return response()->json([
                'success' => false,
                'message' => "Noma'lum xato. Qayta urinib ko'ring."
            ], 500);
            if (preg_match('/^\d{14}$/', $identification_number) || preg_match('/^\d{9}$/', $identification_number)) {
                $pinfl = $identification_number;
                $user = User::query()->where('pinfl', $pinfl)->first();
                if (!$user) {
                    $new_user = $this->userRepository->createOrUpdate($data['person_data']);
                    $this->userRepository->attachRole($new_user, UserRoleEnum::BUYURTMACHI->value);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Noma'lum xato. Qayta urinib ko'ring."
                ], 500);
            }
        } else {
            $encodedData = request('token');
            $decodedData = base64_decode($encodedData);
            list($pinfl, $accessToken) = explode(':', $decodedData);
        }

        $user = User::query()->where('pin', $pinfl)->first();
        if ($user) {
            Auth::login($user);
            $user = Auth::user();
            if ($user->user_status_id != UserStatusEnum::ACTIVE) return $this->sendError('Kirish huquqi mavjud emas', code: 401);
            $roleId = request('role_id');
            $role = Role::query()->find($roleId);
            $token = JWTAuth::claims(['role_id' => $roleId])->fromUser($user);
            if (is_null($user->director_full_name) and preg_match('/^\d{9}$/', (int)$pinfl)) {
                $get_company_info = $this->invoiceService->getCompanyInfo($pinfl);
                if ($get_company_info) {
                    $user_data = ['director_full_name' => $get_company_info['director'], 'pinfl' => $pinfl];
                    $this->userRepository->createOrUpdate($user_data);
                }
            }

            $success['token'] = $token;
            $success['id'] = $user->id;
            $success['full_name'] = $user->full_name;
            $success['pinfl'] = $user->pin;
            $success['role'] = new RoleResource($role);
            $success['status'] = new UserStatusResource($user->status);
            $success['region'] = $user->region_id ? new RegionResource($user->region) : null;
            $success['district'] = $user->district_id ? new DistrictResource($user->district) : null;
            $success['image'] = $user->image ? Storage::disk('public')->url($user->image) : null;
            return $this->sendSuccess($success, 'User logged in successfully.');
        } else {
            return $this->sendError('Kirish huquqi mavjud emas', code: 401);
        }
    }


    public function auth(): JsonResponse
    {
        if (Auth::attempt(['login' => request('username'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->user_status_id != 1) return $this->sendError('Kirish huquqi mavjud emas', code: 401);
            $roleId = request('role_id');
            $role = Role::query()->find($roleId);
            $token = JWTAuth::claims(['role_id' => $roleId])->fromUser($user);

            if (\request('app_id')) {
                $user->update([
                    'notification_app_id' => request('app_id'),
                ]);
            }
            $success['token'] = $token;
            $success['full_name'] = $user->full_name;
            $success['pinfl'] = $user->pin;
            $success['role'] = $role ? new RoleResource($role) : null;
            $success['status'] = new UserStatusResource($user->status);
            $success['region'] = $user->region_id ? new RegionResource($user->region) : null;
            $success['district'] = $user->district_id ? new DistrictResource($user->district) : null;
            $success['image'] = $user->image ? Storage::disk('public')->url($user->image) : null;

            return $this->sendSuccess($success, 'User logged in successfully.');
        } else {
            return $this->sendError('Unauthorised.', code: 401);
        }
    }

    public function checkUser(): JsonResponse
    {
        try {
            $url = 'https://sso.egov.uz/sso/oauth/Authorization.do?grant_type=one_authorization_code
            &client_id=' . config('services.oneId.id') .
                '&client_secret=' . config('services.oneId.secret') .
                '&code=' . request('code') .
                '&redirect_url=' . config('services.oneId.redirect');
            $resClient = Http::post($url);
            $response = json_decode($resClient->getBody(), true);


            $url = 'https://sso.egov.uz/sso/oauth/Authorization.do?grant_type=one_access_token_identify
            &client_id=' . config('services.oneId.id') .
                '&client_secret=' . config('services.oneId.secret') .
                '&access_token=' . $response['access_token'] .
                '&Scope=' . $response['scope'];
            $resClient = Http::post($url);
            $data = json_decode($resClient->getBody(), true);


            $user = User::query()
                ->where('pin', $data['pin'])
                ->where('active', 1)
                ->where('user_status_id', UserStatusEnum::ACTIVE->value)
                ->first();

            if (!$user) {
                $inn = isset($data['pkcs_legal_tin']);
                $person_data = [
                    "name" => $data['first_name'],
                    "surname" => $data['sur_name'],
                    "middle_name" => $data['mid_name'],
                    "pinfl" => $data['pin'],
                    "login" => $inn ? $data['pkcs_legal_tin'] : $data['pport_no'],
                    "password" => Hash::make($data['pin']),
                    "active" => 1,
                    "user_status_id" => 1,
                ];
                if ($inn) {
                    $get_company_info = $this->invoiceService->getCompanyInfo($data['pkcs_legal_tin']);
                    $person_data['organization_name'] = $get_company_info['shortName'];
                    $person_data['director_full_name'] = $get_company_info['director'];
                }
                $new_user = $this->userRepository->createOrUpdate($person_data);
                $this->userRepository->attachRole($new_user, UserRoleEnum::BUYURTMACHI->value);
            }
            if ($user->active == 0) throw new ModelNotFoundException('Foydalanuvchi faol emas');

            if (request('app_id')) {
                $user->update([
                    'notification_app_id' => request('app_id'),
                ]);
            }

            $combinedData = $data['pin'] . ':' . $response['access_token'];

            $encodedData = base64_encode($combinedData);

            $meta = [
                'roles' => RoleResource::collection($user->roles),
                'access_token' => $encodedData,
                'full_name' => $user->full_name
            ];

            return $this->sendSuccess($meta, 'Success');

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), $exception->getCode());
        }
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->sendSuccess(null, 'Logged out successfully.');
    }
}
