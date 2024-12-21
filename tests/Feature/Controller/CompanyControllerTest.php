<?php

namespace Tests\Feature\Controller;

use App\Models\Company;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    /**
     * Setup environment testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperadmin();
    }

    /**
     * Test CompanyController@index. Should has these attributes.
     */
    public function test_index_attributes(): void
    {
        $company = Company::factory()->create();

        $url = route('company.index');
        $this->getJson($url)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 1, fn (AssertableJson $json) => $json
                    ->where('id', $company->id)
                    ->where('name', $company->name)
                    ->where('created_at', $company->created_at?->toJSON())
                    ->where('deleted_at', null)
                )
                ->has('links')
                ->has('meta', fn (AssertableJson $json) => $json
                    ->where('current_page', 1)
                    ->where('to', 1)
                    ->where('total', 1)
                    ->where('per_page', 15)
                    ->etc()
                )
            );
    }

    /**
     * Test CompanyController@index. Should ordered by name ascending.
     */
    public function test_index_order(): void
    {
        [$company1, $company2, $company3] = Company::factory()
            ->count(3)
            ->sequence(
                ['name' => 'Cineplex'],
                ['name' => 'ABC'],
                ['name' => 'Sinarmas'],
            )
            ->create();

        $url = route('company.index');
        $this->getJson($url)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.0.id', $company2->id)
                ->where('data.1.id', $company1->id)
                ->where('data.2.id', $company3->id)
                ->etc()
            );
    }

    /**
     * Test CompanyController@store.
     */
    public function test_store(): void
    {
        $form = [
            'name' => 'Kadokawa',
        ];

        $url = route('company.store');
        $response = $this->postJson($url, $form)->assertCreated();
        $company = Company::latest('id')->first();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('id', $company->id)
            ->where('name', $form['name'])
            ->where('created_at', now()->toJSON())
            ->where('deleted_at', null)
        );

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => $form['name'],
            'created_at' => now(),
            'deleted_at' => null,
        ]);
    }

    /**
     * Test CompanyController@show.
     */
    public function test_show(): void
    {
        $company = Company::factory()->create();

        $url = route('company.show', $company);
        $this->getJson($url)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $company->id)
                ->where('name', $company->name)
                ->where('created_at', $company->created_at?->toJSON())
                ->where('deleted_at', null)
            );
    }

    /**
     * Test CompanyController@update.
     */
    public function test_update(): void
    {
        $form = [
            'name' => 'Kadokawa',
        ];

        $company = Company::factory()->create();

        $url = route('company.update', $company);
        $this->putJson($url, $form)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $company->id)
                ->where('name', $form['name'])
            );

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => $form['name'],
        ]);
    }

    /**
     * Test CompanyController@destroy.
     */
    public function test_destroy(): void
    {
        $company = Company::factory()->create();

        $url = route('company.destroy', $company);
        $this->deleteJson($url)->assertJson(fn (AssertableJson $json) => $json
            ->where('id', $company->id)
            ->where('deleted_at', now()->toJSON())
        );

        $this->assertSoftDeleted($company);
    }
}
