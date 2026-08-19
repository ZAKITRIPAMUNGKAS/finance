<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_edit_project_nominal_and_details(): void
    {
        $user = User::factory()->create(['financial_persona' => 'freelancer']);
        $client = Client::create([
            'user_id' => $user->id,
            'name' => 'PT Maju Bersama',
            'contact_person' => 'Budi',
            'phone' => '08123456789',
            'status' => 'active',
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Website Company Profile',
            'category' => 'web_dev',
            'total_revenue' => 5000000,
            'status' => 'in_progress',
        ]);

        $this->assertEquals(5000000, $project->total_revenue);

        // Edit project nominal to 7.500.000
        Livewire::actingAs($user)
            ->test(\App\Livewire\Projects\Index::class)
            ->call('openEditProjectModal', $project->id)
            ->assertSet('projectId', $project->id)
            ->assertSet('name', 'Website Company Profile')
            ->set('total_revenue', '7.500.000')
            ->set('name', 'Website Company Profile & SEO')
            ->call('saveProject')
            ->assertHasNoErrors();

        $project->refresh();
        $this->assertEquals(7500000, $project->total_revenue);
        $this->assertEquals('Website Company Profile & SEO', $project->name);
    }

    public function test_user_can_delete_project(): void
    {
        $user = User::factory()->create(['financial_persona' => 'freelancer']);
        $client = Client::create([
            'user_id' => $user->id,
            'name' => 'Klien Alpha',
            'status' => 'active',
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Logo Design',
            'total_revenue' => 1500000,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('projects', ['id' => $project->id]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Projects\Index::class)
            ->call('deleteProject', $project->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_user_can_mark_project_as_paid_and_record_income(): void
    {
        $user = User::factory()->create(['financial_persona' => 'freelancer']);
        $account = \App\Models\Account::create([
            'user_id' => $user->id,
            'name' => 'BCA Bisnis',
            'type' => 'bank',
            'current_balance' => 1000000,
            'is_active' => true,
        ]);

        $client = Client::create([
            'user_id' => $user->id,
            'name' => 'Klien Beta',
            'status' => 'active',
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Livestreaming Webinar',
            'total_revenue' => 3000000,
            'status' => 'in_progress',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Projects\Index::class)
            ->call('markProjectAsPaid', $project->id, $account->id)
            ->assertHasNoErrors();

        $project->refresh();
        $account->refresh();

        $this->assertEquals('completed', $project->status);
        $this->assertEquals(4000000, $account->current_balance); // 1.000.000 + 3.000.000
        $this->assertDatabaseHas('invoices', [
            'project_id' => $project->id,
            'status' => 'paid',
            'amount' => 3000000,
        ]);
        $this->assertDatabaseHas('transactions', [
            'project_id' => $project->id,
            'type' => 'income',
            'amount' => 3000000,
        ]);
    }

    public function test_user_can_add_cost_to_project_and_deduct_account_balance(): void
    {
        $user = User::factory()->create(['financial_persona' => 'freelancer']);
        $account = \App\Models\Account::create([
            'user_id' => $user->id,
            'name' => 'BCA Bisnis',
            'type' => 'bank',
            'current_balance' => 5000000,
            'is_active' => true,
        ]);

        $client = Client::create([
            'user_id' => $user->id,
            'name' => 'Klien Gamma',
            'status' => 'active',
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Video Iklan Produk',
            'total_revenue' => 10000000,
            'status' => 'in_progress',
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Projects\Index::class)
            ->call('openAddCostModal', $project->id)
            ->set('cost_description', 'Sewa Kamera Sony FX3')
            ->set('cost_amount', '750.000')
            ->set('cost_account_id', $account->id)
            ->call('saveCost')
            ->assertHasNoErrors();

        $account->refresh();
        $project->refresh();

        $this->assertEquals(4250000, $account->current_balance); // 5.000.000 - 750.000
        $this->assertEquals(750000, $project->total_cost);
        $this->assertEquals(9250000, $project->profit); // 10.000.000 - 750.000
        $this->assertDatabaseHas('project_costs', [
            'project_id' => $project->id,
            'description' => 'Sewa Kamera Sony FX3',
            'amount' => 750000,
        ]);
        $this->assertDatabaseHas('transactions', [
            'project_id' => $project->id,
            'type' => 'expense',
            'amount' => 750000,
        ]);
    }
}
