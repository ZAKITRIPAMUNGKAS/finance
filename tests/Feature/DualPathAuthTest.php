<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifyEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class DualPathAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_registration_triggers_registered_event_and_redirects_to_verify_notice()
    {
        Event::fake([Registered::class]);

        Livewire::test(Register::class)
            ->set('name', 'Andi Pratama')
            ->set('email', 'andi@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('users', [
            'email' => 'andi@example.com',
            'email_verified_at' => null,
        ]);

        Event::assertDispatched(Registered::class);
    }

    public function test_unverified_user_cannot_access_dashboard()
    {
        $unverifiedUser = User::create([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => null,
        ]);

        $this->actingAs($unverifiedUser)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_can_access_dashboard()
    {
        $verifiedUser = User::create([
            'name' => 'Verified User',
            'email' => 'verified@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $this->actingAs($verifiedUser)
            ->get(route('dashboard'))
            ->assertStatus(200);
    }

    public function test_verify_email_livewire_component_can_resend_notification()
    {
        $user = User::create([
            'name' => 'Pending Verification',
            'email' => 'pending@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => null,
        ]);

        Livewire::actingAs($user)
            ->test(VerifyEmail::class)
            ->assertSee('pending@example.com')
            ->call('resendVerification')
            ->assertSet('sent', true);
    }

    public function test_google_oauth_redirect()
    {
        config([
            'services.google.client_id' => 'dummy-client-id',
            'services.google.client_secret' => 'dummy-client-secret',
            'services.google.redirect' => 'http://localhost:8000/auth/google/callback',
        ]);

        $response = $this->get(route('google.redirect'));
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_google_oauth_callback_creates_verified_user_and_starter_accounts()
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-123456');
        $abstractUser->shouldReceive('getEmail')->andReturn('googleuser@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Google User');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);

        $provider = Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        $factory = Mockery::mock(SocialiteFactory::class);
        $factory->shouldReceive('driver')->with('google')->andReturn($provider);

        $this->app->instance(SocialiteFactory::class, $factory);

        $response = $this->get(route('google.callback'));
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'googleuser@gmail.com',
            'google_id' => 'google-id-123456',
        ]);

        $user = User::where('email', 'googleuser@gmail.com')->first();
        $this->assertNotNull($user->email_verified_at);
        $this->assertFalse((bool) $user->onboarding_completed);
    }

    public function test_google_oauth_callback_links_existing_user_account()
    {
        $existingUser = User::create([
            'name' => 'Existing User',
            'email' => 'existing@gmail.com',
            'password' => bcrypt('secret123'),
            'email_verified_at' => null,
            'onboarding_completed' => true,
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-999888');
        $abstractUser->shouldReceive('getEmail')->andReturn('existing@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Existing User');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);

        $provider = Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        $factory = Mockery::mock(SocialiteFactory::class);
        $factory->shouldReceive('driver')->with('google')->andReturn($provider);

        $this->app->instance(SocialiteFactory::class, $factory);

        $response = $this->get(route('google.callback'));
        $response->assertRedirect(route('dashboard'));

        $existingUser->refresh();
        $this->assertEquals('google-id-999888', $existingUser->google_id);
        $this->assertNotNull($existingUser->email_verified_at);
    }
}
