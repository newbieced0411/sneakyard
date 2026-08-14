<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-customers') ?? false;
    }

    public function rules(): array
    {
        return ['admin_notes' => ['nullable', 'string', 'max:3000']];
    }
}
