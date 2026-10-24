<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Siglatrolap');
        $response->assertSee('Manual Login');
    }

    public function test_admin_can_authenticate(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@siglatrolap.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_employee_can_authenticate_with_username(): void
    {
        $response = $this->post('/login', [
            'login' => 'joshuasierra',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_employee_cannot_access_settings(): void
    {
        $employee = User::where('role', 'employee')->first();
        $response = $this->actingAs($employee)->get('/settings');
        $response->assertStatus(403);
    }
}
