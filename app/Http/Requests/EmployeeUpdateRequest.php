<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property Employee $employee
 */
class EmployeeUpdateRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->employee->user)],
            'NIK' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees', 'NIK')->where('company_id', $this->employee->company_id)->ignore($this->employee),
            ],
            'address' => ['nullable', 'string', 'max:1024'],
        ];
    }
}
