<?php

namespace Tests\Feature\Request;

use App\Models\Company;
use App\Models\Employee;
use Tests\TestCase;

class EmployeeStoreRequestTest extends TestCase
{
    private string $url;

    private Company $company;

    /**
     * Setup environment testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperadmin();

        $this->company = Company::factory()->create();
        $this->url = route('employee.store', $this->company);
    }

    /**
     * Test error message when field is not provided or empty.
     */
    public function test_required(): void
    {
        $form = [];

        $this->postJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'name' => __('validation.required', ['attribute' => 'name']),
            'email' => __('validation.required', ['attribute' => 'email']),
            'NIK' => __('validation.required', ['attribute' => 'NIK']),
        ])->assertJsonMissingValidationErrors(['address']);
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

        $this->postJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
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

        $this->postJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
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

        $this->postJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
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
        $employee = Employee::factory()
            ->for($this->company)
            ->create(['NIK' => '1234567890']);

        $form = [
            'NIK' => $employee->NIK,
        ];

        $this->postJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'NIK' => __('validation.unique', ['attribute' => 'NIK']),
        ]);

        $company = Company::factory()->create();
        $this->url = route('employee.store', $company);

        $this->postJson($this->url, $form)->assertJsonMissingValidationErrors(['NIK']);
    }
}
