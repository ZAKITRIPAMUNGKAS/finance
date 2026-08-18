<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\BudgetAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialPersonaAdaptiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_financial_persona_in_settings(): void
    {
        $user = User::factory()->create(['financial_persona' => 'freelancer']);

        $this->assertTrue($user->isFreelancer());
        $this->assertFalse($user->isStudent());

        // Switch to Student
        Livewire::actingAs($user)
            ->test(\App\Livewire\Settings\Index::class)
            ->call('setPersona', 'student', app(BudgetAllocationService::class))
            ->assertHasNoErrors()
            ->assertSet('financial_persona', 'student');

        $user->refresh();
        $this->assertEquals('student', $user->financial_persona);
        $this->assertTrue($user->isStudent());
        $this->assertFalse($user->isMerchant());

        // Switch to Merchant
        Livewire::actingAs($user)
            ->test(\App\Livewire\Settings\Index::class)
            ->call('setPersona', 'merchant', app(BudgetAllocationService::class))
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertEquals('merchant', $user->financial_persona);
        $this->assertTrue($user->isMerchant());
    }

    public function test_student_split_bill_tool_calculates_correctly(): void
    {
        $user = User::factory()->create(['financial_persona' => 'student']);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Tools\StudentSplitBill::class)
            ->call('open')
            ->set('totalAmount', '120.000')
            ->set('totalPeople', 4)
            ->assertSet('perPersonAmount', 30000.0);
    }

    public function test_merchant_pricing_calculator_computes_margin_correctly(): void
    {
        $user = User::factory()->create(['financial_persona' => 'merchant']);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Tools\MerchantPricingCalculator::class)
            ->call('open')
            ->set('baseCost', '65.000')
            ->set('packingCost', '3.000')
            ->set('marketplaceFeePercent', '6.5')
            ->set('targetProfitPercent', '30')
            ->assertSee('Harga Jual Rekomendasi');
    }

    public function test_dashboard_renders_for_different_personas(): void
    {
        $studentUser = User::factory()->create(['financial_persona' => 'student']);

        Livewire::actingAs($studentUser)
            ->test(\App\Livewire\Dashboard::class)
            ->assertSee('Mode Pelajar', false)
            ->assertSee('Batas Aman Jajan Hari Ini', false)
            ->assertSee('Evaluasi Anggaran', false)
            ->assertDontSee('Project Freelance Aktif');

        $employeeUser = User::factory()->create(['financial_persona' => 'employee']);

        Livewire::actingAs($employeeUser)
            ->test(\App\Livewire\Dashboard::class)
            ->assertSee('Mode Karyawan', false)
            ->assertSee('Alokasi Gaji 50/30/20', false)
            ->assertSee('Langganan & Tagihan Rutin', false)
            ->assertDontSee('Project Freelance Aktif');

        $merchantUser = User::factory()->create(['financial_persona' => 'merchant']);

        Livewire::actingAs($merchantUser)
            ->test(\App\Livewire\Dashboard::class)
            ->assertSee('Mode Pedagang', false)
            ->assertSee('Estimasi Laba Bersih', false)
            ->assertSee('Nota & Piutang Pelanggan Toko', false)
            ->assertDontSee('Project Freelance Aktif');

        $freelancerUser = User::factory()->create(['financial_persona' => 'freelancer']);

        Livewire::actingAs($freelancerUser)
            ->test(\App\Livewire\Dashboard::class)
            ->assertSee('Project Freelance Aktif', false);
    }

    public function test_onboarding_wizard_sets_persona_and_initializes_system_tour(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => false,
            'financial_persona' => 'freelancer',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\OnboardingWizard::class)
            ->call('saveOnboarding', [
                'persona' => 'student_creator',
                'activeAccounts' => ['bca' => true, 'gopay' => true],
                'accountBalances' => ['bca' => '1500000', 'gopay' => '250000'],
                'monthlyIncome' => '3000000',
            ], app(BudgetAllocationService::class))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('trigger_tour_after_onboarding', true);

        $user->refresh();
        $this->assertTrue($user->onboarding_completed);
        $this->assertEquals('student', $user->financial_persona);
        $this->assertTrue($user->isStudent());
    }
}
