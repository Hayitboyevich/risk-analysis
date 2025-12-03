<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResourceCollection;
use App\Models\UserRole;
use App\Services\HistoryService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    private HistoryService $historyService;

    public function __construct(
        protected UserService $service,
    )
    {
        parent::__construct();
        $this->historyService = new HistoryService('user_histories');
    }
    public function index(): JsonResponse
    {
        $type = request('type', null);
        $query = $this->service->getAllUsers($this->user, $this->roleId, $type);
        $filters = request()->only(['search', 'region_id', 'district_id', 'status', 'role_id']);
//        $users = $this->service->searchByUser($query, $filters)->paginate(request('per_page', 10));
        $users = $this->service->searchByUser($query, $filters)->paginate()->through(function ($item) {
            return (object) $item;
        });

        return $this->sendSuccess(UserResourceCollection::collection($users), 'All Users', pagination($users));
    }


    public function create(UserRequest $request): JsonResponse
    {
        try {
            $existingUser = $this->service->findByPin($request->pinfl);
            if ($existingUser) {
                $this->service->updateUser($existingUser, $request);
                return $this->sendSuccess($existingUser, 'Foydalanuvchi tahrir qilindi.');
            }
            $user = $this->service->createNewUser($request);
            return $this->sendSuccess($user, 'Foydalanuvchi muvaffaqiyatli yaratildi.');

        } catch (\Exception $exception) {
            return $this->sendError('Xatolik aniqlandi', $exception->getMessage());
        }
    }

    public function delete(): JsonResponse
    {
        try {
            $user = $this->service->findById(request('id'));
            if (!$user) return $this->sendError('Foydalanuvchi topilmadi.');
            $user->delete();
            return $this->sendSuccess(null, 'Success');
        }catch (\Exception $exception){
            return $this->sendError('Xatolik aniqlandi', $exception->getMessage());
        }
    }
}
