<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        Gate::allowIf(fn (User $user) => $user->can(Permission::COMPANY_LIST));

        $companies = Company::query()
            ->orderBy('name')
            ->paginate();

        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyStoreRequest $request): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->can(Permission::COMPANY_CREATE));

        $validated = $request->validated();

        $company = new Company();
        $company->name = $validated['name'];
        $company->save();

        return response()->json(CompanyResource::make($company), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): CompanyResource
    {
        Gate::allowIf(fn (User $user) => $user->can(Permission::COMPANY_SHOW));

        return CompanyResource::make($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyUpdateRequest $request, Company $company): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->can(Permission::COMPANY_EDIT));

        $validated = $request->validated();

        $company->name = $validated['name'];
        $company->save();

        return response()->json($company->only(['id', 'name']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->can(Permission::COMPANY_DELETE));

        $company->delete();

        return response()->json($company->only(['id', 'deleted_at']));
    }
}
