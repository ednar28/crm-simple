<?php

namespace Tests\Feature\Policy;

use App\Enums\Permission;
use App\Models\Company;
use Tests\TestCase;

class CompanyPolicyTest extends TestCase
{
    /**
     * Test CompanyController@index.
     */
    public function test_index(): void
    {
        $url = route('company.index');

        $this->assertUserPermission(fn () => $this->getJson($url))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::COMPANY_LIST))
            ->forbid($this->createAdmin());
    }

    /**
     * Test CompanyController@store.
     */
    public function test_store(): void
    {
        $url = route('company.store');

        $form = [
            'name' => 'Kadokawa',
        ];

        $this->assertUserPermission(fn () => $this->postJson($url, $form))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::COMPANY_CREATE))
            ->forbid($this->createAdmin());
    }

    /**
     * Test CompanyController@show.
     */
    public function test_show(): void
    {
        $company = Company::factory()->create();
        $url = route('company.show', $company);

        $this->assertUserPermission(fn () => $this->getJson($url))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::COMPANY_SHOW))
            ->forbid($this->createAdmin());
    }

    /**
     * Test CompanyController@update.
     */
    public function test_update(): void
    {
        $company = Company::factory()->create();
        $url = route('company.update', $company);

        $form = [
            'name' => 'Kadokawa',
        ];

        $this->assertUserPermission(fn () => $this->putJson($url, $form))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::COMPANY_EDIT))
            ->forbid($this->createAdmin());
    }

    /**
     * Test CompanyController@destroy.
     */
    public function test_destroy(): void
    {
        $company = Company::factory()->create();
        $url = route('company.destroy', $company);

        $this->assertUserPermission(fn () => $this->deleteJson($url))
            ->allow($this->createSuperadmin())
            ->allow($this->createAdmin(Permission::COMPANY_DELETE))
            ->forbid($this->createAdmin());
    }
}
