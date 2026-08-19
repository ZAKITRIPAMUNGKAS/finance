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
}
