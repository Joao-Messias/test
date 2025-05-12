<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se um usuário ativo pode fazer login com sucesso.
     */
    public function test_active_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'status' => true
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertAuthenticated();
    }

    /**
     * Testa se um usuário inativo não pode fazer login.
     */
    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create([
            'password' => bcrypt('password123')
        ]);

        $this->get(route('login'));

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Testa se um usuário com credenciais inválidas não pode fazer login.
     */
    public function test_user_with_invalid_credentials_cannot_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $this->get(route('login'));

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Testa se um usuário não existente não pode fazer login.
     */
    public function test_nonexistent_user_cannot_login(): void
    {
        $this->get(route('login'));

        $response = $this->post(route('login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Testa se o usuário é redirecionado corretamente após o login.
     */
    public function test_user_is_redirected_to_intended_url_after_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->get(route('tasks.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertAuthenticated();
    }

    /**
     * Testa se o usuário pode fazer logout.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Testa se o usuário pode usar a funcionalidade "lembrar-me".
     */
    public function test_user_can_use_remember_me(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $this->get(route('login'));

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
            'remember' => true,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertAuthenticated();

        $cookies = $response->headers->getCookies();
        
        $rememberCookie = collect($cookies)->first(function ($cookie) {
            return str_starts_with($cookie->getName(), 'remember_web');
        });

        $this->assertNotNull($rememberCookie, 'Cookie remember_web não encontrado');
        $this->assertTrue($rememberCookie->isHttpOnly());
        $this->assertTrue($rememberCookie->isSecure());
    }
} 