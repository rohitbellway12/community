<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return array_merge(
            (new UserResource($this))->toArray($request),
            [
                'membership_role'    => $this->whenPivotLoaded('group_user', fn () => $this->pivot->role ?? 'member'),
                'membership_status'  => $this->whenPivotLoaded('group_user', fn () => $this->pivot->status ?? 'active'),
                'joined_at'          => $this->whenPivotLoaded('group_user', fn () => $this->pivot->created_at?->toIso8601String()),
            ]
        );
    }
}
