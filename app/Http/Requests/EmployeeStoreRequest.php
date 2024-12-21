<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property \App\Models\Company $company
 */
class EmployeeStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')],
            'NIK' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'NIK')->where('company_id', $this->company->id),
            ],
            'address' => ['nullable', 'string', 'max:1024'],
        ];
    }
}
