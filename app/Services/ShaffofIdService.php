<?php

namespace App\Services;

use App\Enums\UserStatusEnum;
use App\Http\Resources\RoleResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShaffofIdService
{
    public function handleCallback(string $code, string $codeVerifier): ?array
    {
        $baseUrl = rtrim(config('app.shaffofId.url'));

        $tokenPayload = [
            'grant_type' => 'authorization_code',
            'client_id' => config('app.shaffofId.client_id'),
            'client_secret' => config('app.shaffofId.client_secret'),
            'redirect_uri' => config('app.shaffofId.redirect_url'),
            'code' => $code,
        ];
        if ($codeVerifier) {
            $tokenPayload['code_verifier'] = $codeVerifier;
        }

        $tokenResponse = Http::asForm()->timeout(10)->post("{$baseUrl}/oauth/token", $tokenPayload);

        if (!$tokenResponse->successful()) {
            return null;
        }



        $accessToken = $tokenResponse->object()->access_token ?? null;
        $idToken = $tokenResponse->object()->id_token ?? null;
        if (!$accessToken) {
            return null;
        }

        $userInfoResponse = Http::withToken($accessToken)->timeout(10)->get("{$baseUrl}/oauth/userinfo");
        if (!$userInfoResponse->successful()) {
            Log::warning('ShaffofID: userinfo so\'rovi muvaffaqiyatsiz', [
                'status' => $userInfoResponse->status(),
            ]);
            return null;
        }

        $userInfo = $userInfoResponse->object();
        $isLegal = ($userInfo->subject_type ?? null) === 'legal';


        $pin = $isLegal ? $userInfo->organization->tin : $userInfo->pinfl;

        $organizationName = $isLegal
            ? ($userInfo->organization->name
                ?? $userInfo->organization->full_name
                ?? $userInfo->organization->short_name
                ?? $userInfo->organization->title
                ?? null)
            : null;

        $phone = $userInfo->phone_number ?? null;
        $userType = $isLegal ? 'Yuridik' : 'Jismoniy';

        if (!$pin) {
            Log::warning('ShaffofID: userinfo\'da pinfl ham, tin ham yo\'q', [
                'sub' => $userInfo->sub ?? null,
            ]);
            return null;
        }


        $user = User::query()
            ->where('pinfl', $pin)
            ->where('user_status_id', UserStatusEnum::ACTIVE->value)
            ->where('active', 1)
            ->first();

        if (!$user) throw new ModelNotFoundException('Foydalanuvchi topilmadi');

        if ($user->active == 0) throw new ModelNotFoundException('Foydalanuvchi faol emas');

        $combinedData = $pin . ':' . $accessToken;

        $encodedData = base64_encode($combinedData);

        $user->update(['id_token' => $idToken ?? null]);

        return [
            'roles' => RoleResource::collection($user->roles),
            'full_name' => $user->full_name ? $user->full_name : ($organizationName ?? null),
            'access_token' => $encodedData,
            'user_type' => $userType,
        ];
    }


    public function logout($user)
    {
        try {
            $url = rtrim(config('app.shaffofId.url'), '/') . '/oauth/logout';

            Log::channel('logout')->info('Shaffof logout started', [
                'user' => [
                    'id' => $user->id ?? null,
                ],

                'config' => [
                    'url' => config('app.shaffofId.url'),
                    'client_id' => config('app.shaffofId.client_id'),
                    'redirect_url' => config('app.shaffofId.redirect_url'),
                ],

                'request' => [
                    'method' => 'GET',
                    'url' => $url,
                    'client_id' => config('app.shaffofId.client_id'),
                    'post_logout_redirect_uri' => config('app.shaffofId.redirect_url'),
                    'id_token_exists' => !empty($user->id_token),
                    'id_token_length' => !empty($user->id_token)
                        ? strlen($user->id_token)
                        : 0,
                ],
            ]);

            $response = Http::withoutVerifying()
                ->timeout(10)
                ->get(
                    $url,
                    [
                        'client_id' => config('app.shaffofId.client_id'),
                        'id_token' => $user->id_token,
                        'post_logout_redirect_uri' => config('app.shaffofId.redirect_url'),
                    ]
                );

            Log::channel('logout')->info('Shaffof logout response', [
                'user_id' => $user->id ?? null,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'failed' => $response->failed(),
                'headers' => $response->headers(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                Log::channel('logout')->info('Shaffof logout success', [
                    'user_id' => $user->id,
                    'status' => $response->status(),
                ]);

                return true;
            }

            Log::channel('logout')->error('Shaffof logout failed', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Throwable $exception) {

            Log::channel('logout')->error('Shaffof logout exception', [
                'user_id' => $user->id ?? null,
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return false;
        }
    }
}
