<?php

namespace Tests\Feature;

use App\Livewire\Budgets\Index as BudgetsIndex;
use App\Models\BudgetCategory;
use App\Models\BudgetGroup;
use App\Models\BudgetProfile;
use App\Models\Category;
use App\Models\User;
use App\Services\BudgetAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BudgetAllocationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected BudgetAllocationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Zaki Pratama',
            'email' => 'zaki@example.com',
        ]);

        $this->service = app(BudgetAllocationService::class);
        $this->service->seedInitialBudgetConfiguration($this->user->id);
    }

    public function test_budget_engine_seeds_six_groups_and_active_profile()
    {
        $this->assertDatabaseCount('budget_groups', 6);
        $this->assertDatabaseHas('budget_profiles', [
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('budget_categories', [
            'priority_tier' => 1,
        ]);
    }

    public function test_income_floor_and_cv_calculation()
    {
        $floorData = $this->service->calculateIncomeFloor($this->user->id);

        $this->assertArrayHasKey('income_floor', $floorData);
        $this->assertArrayHasKey('cv_value', $floorData);
        $this->assertArrayHasKey('suggested_method', $floorData);
        $this->assertGreaterThanOrEqual(0, $floorData['income_floor']);
    }

    public function test_surplus_waterfall_allocation()
    {
        $income = 15000000;
        $floor = 8000000;
        $waterfall = $this->service->calculateSurplusAllocation($income, $floor);

        $this->assertEquals(7000000, $waterfall['surplus_amount']);
        $this->assertCount(4, $waterfall['steps']);
        $this->assertEquals(7000000, $waterfall['total_allocated']);
    }

    public function test_z_score_feasibility_validation()
    {
        $historical = [10.0, 12.0, 11.0, 10.5, 11.5]; // mean = 11, stdDev ≈ 0.79

        // Realistic (close to mean)
        $resRealistic = $this->service->validateZScore(11.2, $historical);
        $this->assertEquals('realistic', $resRealistic['status']);

        // Extreme (outlier)
        $resUnrealistic = $this->service->validateZScore(30.0, $historical);
        $this->assertEquals('unrealistic', $resUnrealistic['status']);
    }

    public function test_livewire_budget_index_component_renders_and_saves()
    {
        $this->actingAs($this->user);

        Livewire::test(BudgetsIndex::class)
            ->assertStatus(200)
            ->assertSee('Adaptive Budget Allocation')
            ->assertSee('Income Floor (P25)')
            ->call('openConfigModal')
            ->assertSet('isConfigModalOpen', true)
            ->call('applyEmaSuggestions')
            ->call('saveConfiguration');
    }
}
