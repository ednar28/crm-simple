<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssertAuthPermission
{
    /**
     * Constructor.
     *
     * @param  \Closure(User $user): void  $authenticate
     * @param  \Closure(): \Illuminate\Testing\TestResponse  $do
     */
    public function __construct(private \Closure $authenticate, private \Closure $do) {}

    /**
     * Authenticate user, run the prerequisite http request and return the test response.
     */
    private function requestAs(User $user): \Illuminate\Testing\TestResponse
    {
        $this->authenticate->__invoke($user);

        return $this->do->__invoke();
    }

    /**
     * Assert http result is forbidden.
     */
    public function forbid(User $user)
    {
        DB::beginTransaction();

        $this->requestAs($user)->assertForbidden();

        // logout user
        \Auth::logout();

        DB::rollBack();

        return $this;
    }

    /**
     * Assert http result is successful.
     */
    public function allow(User $user)
    {
        DB::beginTransaction();

        $this->requestAs($user)->assertSuccessful();

        // logout user
        \Auth::logout();

        DB::rollBack();

        return $this;
    }
}
