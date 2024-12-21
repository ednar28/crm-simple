<?php

namespace Tests\Feature\Policy;

use App\Enums\Permission;
use App\Models\Company;
use App\Models\Employee;
use Tests\TestCase;

class EmployeePolicyTest extends TestCase
{
    /**
     * Test EmployeeController@index.
     */
    public function test_index(): void
    {
        $company = Company::factory()->create();

        $url = route('employee.index', $company);
        $this->assertUserPermission(fn () => $this->getJson($url))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::EMPLOYEE_LIST))
            ->allow($this->createManagerCompany($company))
            ->allow($this->createEmployee($company))
            ->forbid($this->createManagerCompany())
            ->forbid($this->createAdmin());
    }

    /**
     * Test EmployeeController@store.
     */
    public function test_store(): void
    {
        $form = [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'NIK' => '1234567890',
            'address' => 'Jl. Raya',
        ];

        $company = Company::factory()->create();

        $url = route('employee.store', $company);
        $this->assertUserPermission(fn () => $this->postJson($url, $form))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::EMPLOYEE_CREATE))
            ->allow($this->createManagerCompany($company))
            ->forbid($this->createEmployee($company))
            ->forbid($this->createManagerCompany())
            ->forbid($this->createEmployee())
            ->forbid($this->createAdmin());
    }

    /**
     * Test EmployeeController@show.
     */
    public function test_show(): void
    {
        $employee = Employee::factory()->create();

        $url = route('employee.show', $employee);
        $this->assertUserPermission(fn () => $this->getJson($url))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::EMPLOYEE_SHOW))
            ->allow($this->createManagerCompany($employee->company))
            ->allow($this->createEmployee($employee->company))
            ->forbid($this->createManagerCompany())
            ->forbid($this->createEmployee())
            ->forbid($this->createAdmin());
    }

    /**
     * Test EmployeeController@update.
     */
    public function test_update(): void
    {
        $employee = Employee::factory()->create();

        $url = route('employee.update', $employee);

        $form = [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'NIK' => '1234567890',
            'address' => 'Jl. Raya',
        ];

        $this->assertUserPermission(fn () => $this->putJson($url, $form))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::EMPLOYEE_EDIT))
            ->allow($this->createManagerCompany($employee->company))
            ->forbid($this->createEmployee($employee->company))
            ->forbid($this->createManagerCompany())
            ->forbid($this->createEmployee())
            ->forbid($this->createAdmin());
    }

    /**
     * Test EmployeeController@destroy.
     */
    public function test_destroy(): void
    {
        $employee = Employee::factory()->create();

        $url = route('employee.destroy', $employee);

        $this->assertUserPermission(fn () => $this->deleteJson($url))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::EMPLOYEE_DELETE))
            ->allow($this->createManagerCompany($employee->company))
            ->forbid($this->createEmployee($employee->company))
            ->forbid($this->createManagerCompany())
            ->forbid($this->createEmployee())
            ->forbid($this->createAdmin());
    }
}
