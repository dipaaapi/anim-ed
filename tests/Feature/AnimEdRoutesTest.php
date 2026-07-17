<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AnimEdRoutesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test public home route.
     */
    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('anim-ed');
    }

    /**
     * Test browse page route.
     */
    public function test_browse_page_returns_successful_response(): void
    {
        $response = $this->get('/browse?type=anime');

        $response->assertStatus(200);
    }

    /**
     * Test hidden admin login route.
     */
    public function test_hidden_admin_login_page_loads_correctly(): void
    {
        $adminPath = env('ADMIN_LOGIN_PATH', 'secret-portal-admin');
        $response = $this->get('/' . $adminPath);

        $response->assertStatus(200);
        $response->assertSee('System Control Portal');
    }

    /**
     * Test dashboard is protected from unauthorized users.
     */
    public function test_admin_dashboard_redirects_for_guests(): void
    {
        $response = $this->get('/admin/dashboard');

        // Middleware throws 403 abort
        $response->assertStatus(403);
    }

    /**
     * Test admin dashboard loads for authenticated admin.
     */
    public function test_admin_dashboard_loads_for_admin_user(): void
    {
        $adminUser = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('System Overview');
    }
}
