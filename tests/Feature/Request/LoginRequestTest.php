<?php

namespace Tests\Feature\Request;

use App\Models\User;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    private string $url;

    /**
     * Setup environment testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->url = route('auth.login');
    }

    /**
     * Test error message when field is not provided or empty.
     */
    public function test_required(): void
    {
        $form = [];

        $this->postJson($this->url, $form)->assertJsonValidationErrors([
            'email' => __('validation.required', ['attribute' => 'email']),
            'password' => __('validation.required', ['attribute' => 'password']),
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

        $this->postJson($this->url, $form)->assertJsonValidationErrors([
            'email' => __('validation.email', ['attribute' => 'email']),
        ]);
    }

    /**
     * Test error message when field is not a string.
     */
    public function test_string(): void
    {
        $form = [
            'password' => ['key' => 'value'],
        ];

        $this->postJson($this->url, $form)->assertJsonValidationErrors([
            'password' => __('validation.string', ['attribute' => 'password']),
        ]);
    }

    /**
     * Test error message when credentials do not match.
     */
    public function test_failed(): void
    {
        $user = User::factory()->create([
            'email' => 'rizky@example.test',
            'password' => bcrypt('venom'),
        ]);

        // wrong email
        $form = [
            'email' => 'd4K9I@example.com',
            'password' => 'venom',
        ];
        $this->postJson($this->url, $form)->assertJsonValidationErrors([
            'email' => __('auth.failed'),
        ]);

        // wrong password
        $form = [
            'email' => $user->email,
            'password' => 'wrong-password',
        ];
        $this->postJson($this->url, $form)->assertJsonValidationErrors([
            'email' => __('auth.failed'),
        ]);
    }
}
