<?php

namespace Tests\Feature\Request;

use App\Models\Employee;
use App\Models\User;
use Tests\TestCase;

class EmployeeUpdateRequestTest extends TestCase
{
    private string $url;

    private Employee $employee;

    /**
     * Setup environment testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperadmin();

        $this->employee = Employee::factory()->create();
        $this->url = route('employee.update', $this->employee);
    }

    /**
     * Test error message when field is not provided or empty.
     */
    public function test_required(): void
    {
        $form = [];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'name' => __('validation.required', ['attribute' => 'name']),
            'email' => __('validation.required', ['attribute' => 'email']),
            'NIK' => __('validation.required', ['attribute' => 'NIK']),
        ]);
    }

    /**
     * Test error message when field is not a string.
     */
    public function test_string(): void
    {
        $form = [
            'name' => ['key' => 'value'],
            'NIK' => ['key' => 'value'],
            'address' => ['key' => 'value'],
        ];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'name' => __('validation.string', ['attribute' => 'name']),
            'NIK' => __('validation.string', ['attribute' => 'NIK']),
            'address' => __('validation.string', ['attribute' => 'address']),
        ]);
    }

    /**
     * Test error message when field is not a email.
     */
    public function test_email(): void
    {
        $form = [
            'email' => 'some text',
        ];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'email' => __('validation.email', ['attribute' => 'email']),
        ]);
    }

    /**
     * Test error message when field is too long.
     */
    public function test_max_length(): void
    {
        $randomString = str()->random(1025);
        $form = [
            'name' => $randomString,
            'email' => $randomString,
            'NIK' => $randomString,
            'address' => $randomString,
        ];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'name' => __('validation.max.string', ['attribute' => 'name', 'max' => 100]),
            'email' => __('validation.max.string', ['attribute' => 'email', 'max' => 100]),
            'NIK' => __('validation.max.string', ['attribute' => 'NIK', 'max' => 20]),
            'address' => __('validation.max.string', ['attribute' => 'address', 'max' => 1024]),
        ]);
    }

    /**
     * Test error message when field is already exist in database.
     */
    public function test_unique(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->for($this->employee->company)->create();

        $form = [
            'email' => $employee->user->email,
            'NIK' => $employee->NIK,
        ];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'email' => __('validation.unique', ['attribute' => 'email']),
            'NIK' => __('validation.unique', ['attribute' => 'NIK']),
        ]);
    }
}
