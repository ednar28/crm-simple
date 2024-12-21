<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Http\Resources\EmployeeCollection;
use App\Http\Resources\EmployeeResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company): EmployeeCollection
    {
        Gate::allowIf(fn (User $user) => $user->canManageCompany($company, Permission::EMPLOYEE_LIST));

        $employees = Employee::query()
            ->select('employees.*')
            ->filterByRole()
            ->with(['user'])
            ->where('company_id', $company->id)
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->orderBy('name')
            ->paginate();

        return new EmployeeCollection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeStoreRequest $request, Company $company): JsonResponse
    {
        Gate::allowIf(fn (User $user) => $user->canManageCompany($company, Permission::EMPLOYEE_CREATE, true));

        $validated = $request->validated();

        $employee = DB::transaction(function () use ($company, $validated) {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = bcrypt(str()->random(10));
            $user->save();

            $employee = new Employee();
            $employee->user()->associate($user);
            $employee->company()->associate($company);
            $employee->NIK = $validated['NIK'];
            $employee->address = $validated['address'] ?? null;
            $employee->save();

            // TODO send email to user.
            return $employee;
        });

        return response()->json(
            [
                'id' => $employee->id,
                'name' => $employee->user?->name,
                'created_at' => $employee->created_at?->toJSON(),
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): EmployeeResource
    {
        /** @var Company */
        $company = $employee->company;
        Gate::allowIf(fn (User $user) => $user->canManageCompany($company, Permission::EMPLOYEE_SHOW));

        return EmployeeResource::make($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeUpdateRequest $request, Employee $employee): JsonResponse
    {
        /** @var Company */
        $company = $employee->company;
        Gate::allowIf(fn (User $user) => $user->canManageCompany($company, Permission::EMPLOYEE_EDIT, true));

        $validated = $request->validated();

        $employee = DB::transaction(function () use ($employee, $validated) {
            /** @var User */
            $user = $employee->user;
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->save();

            $employee->NIK = $validated['NIK'];
            $employee->address = $validated['address'] ?? null;
            $employee->save();

            return $employee;
        });

        return response()->json($employee->only(['id', 'updated_at']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): JsonResponse
    {
        $company = $employee->company;
        Gate::allowIf(fn (User $user) => $user->canManageCompany($company, Permission::EMPLOYEE_DELETE, true));

        $employee->delete();

        return response()->json($employee->only(['id', 'deleted_at']));
    }
}
