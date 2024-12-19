<?php

namespace Tests\Feature\Controller;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * Test AuthController@login.
     */
    public function test_login(): void
    {
        $this->assertGuest();

        $user = User::factory()->create(['email' => 'rizky@example.test']);

        $form = [
            'email' => 'rizky@example.test',
            'password' => 'password',
        ];

        $url = route('auth.login');
        $this->postJson($url, $form)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->whereNot('access_token', null)
                ->where('token_type', 'bearer')
                ->where('expires_in', 3600)
            );

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test AuthController@refresh.
     */
    public function test_refresh(): void
    {
        $user = User::factory()->create();
        $headers = $this->generateHeaders($user);

        $url = route('auth.refresh');
        $this->postJson($url, headers: $headers)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->whereNot('access_token', null)
                ->where('token_type', 'bearer')
                ->where('expires_in', 3600)
            );
    }

    /**
     * Test AuthController@me.
     */
    public function test_me(): void
    {
        $user = User::factory()->create();
        $headers = $this->generateHeaders($user);

        $url = route('auth.me');
        $this->getJson($url, $headers)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $user->id)
                ->where('name', $user->name)
                ->where('email', $user->email)
            );
    }

    /**
     * Test AuthController@logout.
     */
    public function test_logout(): void
    {
        $user = User::factory()->create();
        $headers = $this->generateHeaders($user);

        $url = route('auth.logout');
        $this->postJson($url, headers: $headers)->assertOk();

        // try access after logout
        $this->postJson($url, headers: $headers)->assertUnauthorized();
    }

    /**
     * Generate a headers for authenticated user.
     *
     * @return array{Accept: string, Authorization: string}
     */
    private function generateHeaders(User $user): array
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . JWTAuth::fromUser($user),
        ];
    }
}
