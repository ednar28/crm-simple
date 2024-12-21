<?php

namespace Tests\Feature\Request;

use App\Models\Company;
use Tests\TestCase;

class CompanyUpdateRequestTest extends TestCase
{
    private Company $company;

    private string $url;

    /**
     * Setup environment testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperadmin();

        $this->company = Company::factory()->create();
        $this->url = route('company.update', $this->company);
    }

    /**
     * Test error message when field is not provided or empty.
     */
    public function test_required(): void
    {
        $form = [];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'name' => __('validation.required', ['attribute' => 'name']),
        ]);
    }

    /**
     * Test error message when field is not a string.
     */
    public function test_string(): void
    {
        $form = [
            'name' => ['key' => 'value'],
        ];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'name' => __('validation.string', ['attribute' => 'name']),
        ]);
    }

    /**
     * Test error message when field is too long.
     */
    public function test_max_length(): void
    {
        $randomString = str()->random(500);
        $form = [
            'name' => $randomString,
        ];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'name' => __('validation.max.string', ['attribute' => 'name', 'max' => 100]),
        ]);
    }

    /**
     * Test error message when field is already exist in database.
     */
    public function test_unique(): void
    {
        $company = Company::factory()->create();

        $form = [
            'name' => $company->name,
        ];

        $this->putJson($this->url, $form)->assertUnprocessable()->assertJsonValidationErrors([
            'name' => __('validation.unique', ['attribute' => 'name']),
        ]);

        $form['name'] = $this->company->name;
        $this->putJson($this->url, $form)->assertJsonMissingValidationErrors(['name']);
    }
}
