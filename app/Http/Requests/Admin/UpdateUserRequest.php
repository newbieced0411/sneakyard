<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-users') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where(fn (Builder $query): Builder => $query->where('slug', '!=', 'customer')),
            ],
            'password' => ['nullable', 'confirmed', Password::min(10)->mixedCase()->numbers()],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
