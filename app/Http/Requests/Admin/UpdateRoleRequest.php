<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

final class UpdateRoleRequest extends StoreRoleRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($this->route('role'))],
        ];
    }
}
