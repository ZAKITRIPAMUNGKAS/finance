<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AccountBalanceUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_edit_account_with_dots_formatting(): void
    {
        $user = User::factory()->create();

        // 1. Create ShopeePay with formatted dots
        Livewire::actingAs($user)
            ->test(\App\Livewire\Accounts\Index::class)
            ->set('name', 'ShopeePay')
            ->set('type', 'ewallet')
            ->set('initial_balance', '150.000')
            ->call('saveAccount')
            ->assertHasNoErrors();

        $account = Account::where('user_id', $user->id)->where('name', 'ShopeePay')->first();
        $this->assertNotNull($account);
        $this->assertEquals(150000, (float) $account->current_balance);
        $this->assertEquals(150000, (float) $account->initial_balance);

        // 2. Edit account balance via pencil icon modal
        Livewire::actingAs($user)
            ->test(\App\Livewire\Accounts\Index::class)
            ->call('openEditModal', $account->id)
            ->assertSet('name', 'ShopeePay')
            ->assertSet('initial_balance', '150.000')
            ->set('initial_balance', '350.000')
            ->call('saveAccount')
            ->assertHasNoErrors();

        $account->refresh();
        $this->assertEquals(350000, (float) $account->current_balance);
    }
}
