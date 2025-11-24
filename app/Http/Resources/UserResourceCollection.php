<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class UserResourceCollection extends ResourceCollection
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
            'organization_name' => $this->organization_name,
            'roles' => RoleResource::collection($this->roles),
            'phone' => $this->phone,
            'pinfl' => $this->pinfl,
            'login' => $this->login,
            'status' => UserStatusResource::make($this->status),
            'image' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'files' => DocumentResource::collection($this->documents)
        ];
    }
}
