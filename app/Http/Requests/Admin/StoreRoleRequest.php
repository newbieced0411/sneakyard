<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\AdminPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-roles') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', Rule::enum(AdminPermission::class)],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! in_array(AdminPermission::AccessAdmin->value, $this->input('permissions', []), true)) {
                $validator->errors()->add('permissions', 'Every staff role must include admin access.');
            }
        }];
    }
}
