<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class UserResourceCollection extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'name' => $this->name,
            'region' => RegionResource::make($this->region) ?? null,
            'district' => DistrictResource::make($this->district) ?? null,
            'middle_name' => $this->middle_name,
            'organization_name' => $this->organization_name ?? null,
            'roles' => $this->roles ? RoleResource::collection($this->roles) : null,
            'phone' => $this->phone,
            'pinfl' => $this->pin,
            'login' => $this->login,
            'status' => UserStatusResource::make($this->status),
            'image' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'files' => $this->documents ? DocumentResource::collection($this->documents) : null,
        ];
    }
}
