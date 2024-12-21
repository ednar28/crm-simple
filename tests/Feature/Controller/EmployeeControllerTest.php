<?php

namespace Tests\Feature\Controller;

use App\Enums\Role;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
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
     * Test EmployeeController@index. Should has these attributes.
     */
    public function test_index_attributes(): void
    {
        $employee = Employee::factory()->create();

        $url = route('employee.index', $employee->company);
        $this->getJson($url)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 1, fn (AssertableJson $json) => $json
                    ->where('id', $employee->id)
                    ->where('name', $employee->user->name)
                    ->where('email', $employee->user->email)
                    ->where('NIK', $employee->NIK)
                    ->where('created_at', $employee->created_at?->toJSON())
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
     * Test EmployeeController@index. Should ordered by name ascending.
     */
    public function test_index_order(): void
    {
        [$user1, $user2, $user3] = User::factory()
            ->count(3)
            ->sequence(
                ['name' => 'Budi'],
                ['name' => 'Joko'],
                ['name' => 'Anita'],
            )
            ->create();

        $company = Company::factory()->create();
        [$employee1, $employee2, $employee3] = Employee::factory()
            ->for($company)
            ->count(3)
            ->sequence(
                ['user_id' => $user1->id],
                ['user_id' => $user2->id],
                ['user_id' => $user3->id],
            )
            ->create();

        $url = route('employee.index', $company);
        $this->getJson($url)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.0.id', $employee3->id)
                ->where('data.1.id', $employee1->id)
                ->where('data.2.id', $employee2->id)
                ->etc()
            );
    }

    /**
     * Test EmployeeController@index.
     */
    public function test_index_with_check_role(): void
    {
        $company = Company::factory()->create();
        [$employee, $managerCompany] = Employee::factory()->for($company)->count(2)->create();

        $managerCompany->user->assignRole(Role::MANAGER_COMPANY);

        $url = route('employee.index', $company);
        // superadmin
        $this->getJson($url)->assertJsonCount(2, 'data');

        // manager company
        $this->actingAs($managerCompany->user);
        $this->getJson($url)->assertJsonCount(2, 'data');

        // employee
        $this->actingAs($employee->user);
        $this->getJson($url)->assertJsonCount(1, 'data');
    }

    /**
     * Test EmployeeController@store.
     */
    public function test_store(): void
    {
        $form = [
            'name' => 'Budi',
            'email' => '9G6t1@example.com',
            'NIK' => '1234567890',
            'address' => 'Jl. Raya',
        ];

        $company = Company::factory()->create();
        $url = route('employee.store', $company);
        $response = $this->postJson($url, $form)->assertCreated();
        $employee = Employee::latest('id')->first();
        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('id', $employee->id)
            ->where('name', $form['name'])
            ->where('created_at', now()->toJSON())
        );

        $this->assertDatabaseHas('users', [
            'id' => $employee->user_id,
            'name' => $form['name'],
            'email' => $form['email'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'NIK' => $form['NIK'],
            'address' => $form['address'],
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }

    /**
     * Test EmployeeController@show.
     */
    public function test_show(): void
    {
        $employee = Employee::factory()->create();

        $url = route('employee.show', $employee);
        $this->getJson($url)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $employee->id)
                ->where('name', $employee->user->name)
                ->where('email', $employee->user->email)
                ->where('NIK', $employee->NIK)
                ->where('address', $employee->address)
                ->has('company', fn (AssertableJson $json) => $json
                    ->where('id', $employee->company->id)
                    ->where('name', $employee->company->name)
                )
                ->where('created_at', $employee->created_at?->toJSON())
            );
    }

    /**
     * Test EmployeeController@update.
     */
    public function test_update(): void
    {
        $form = [
            'name' => 'Budi',
            'email' => '9G6t1@example.com',
            'NIK' => '1234567890',
            'address' => 'Jl. Raya',
        ];

        $employee = Employee::factory()->create();

        $url = route('employee.update', $employee);
        $this->putJson($url, $form)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $employee->id)
                ->where('updated_at', now()->toJSON())
            );

        $this->assertDatabaseHas('users', [
            'id' => $employee->user_id,
            'name' => $form['name'],
            'email' => $form['email'],
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'NIK' => $form['NIK'],
            'address' => $form['address'],
            'updated_at' => now(),
        ]);
    }

    /**
     * Test EmployeeController@destroy.
     */
    public function test_destroy(): void
    {
        $employee = Employee::factory()->create();

        $url = route('employee.destroy', $employee);
        $this->deleteJson($url)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $employee->id)
                ->where('deleted_at', now()->toJSON())
            );

        $this->assertSoftDeleted($employee);
    }
}
