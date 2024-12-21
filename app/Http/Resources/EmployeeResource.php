<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Employee
 */
class EmployeeResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\User */
        $user = $this->user;

        /** @var \App\Models\Company */
        $company = $this->company;

        return [
            'id' => $this->id,
            'NIK' => $this->NIK,
            'address' => $this->address,

            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $this->created_at?->toJSON(),

            'company' => $company->only(['id', 'name']),
        ];
    }
}
