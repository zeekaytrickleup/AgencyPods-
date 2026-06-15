<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\Goal;
use App\Models\Pod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PodAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $manager;        // manages podA
    private User $otherManager;   // manages podB
    private Pod $podA;
    private Pod $podB;
    private $clientA;
    private Goal $goalA;
    private $sectionA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->makeUser('admin@test.com', 'super_admin');
        $this->manager = $this->makeUser('m1@test.com', 'pod_manager');
        $this->otherManager = $this->makeUser('m2@test.com', 'pod_manager');

        $this->podA = Pod::create(['name' => 'Pod A', 'color' => '#111111', 'manager_id' => $this->manager->id]);
        $this->podB = Pod::create(['name' => 'Pod B', 'color' => '#222222', 'manager_id' => $this->otherManager->id]);

        $this->clientA = $this->podA->clients()->create(['name' => 'Client A', 'industry' => 'Test']);
        $this->goalA = $this->clientA->goals()->create(['title' => 'Goal A']);
        foreach (Goal::SECTION_TYPES as $type) {
            $this->goalA->sections()->create(['type' => $type, 'content' => null]);
        }
        $this->sectionA = $this->goalA->sections()->where('type', 'goal')->first();
    }

    private function makeUser(string $email, string $role): User
    {
        return User::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_super_admin_sees_all_pods(): void
    {
        $this->actingAs($this->admin)->get('/dashboard')
            ->assertOk()->assertSee('Pod A')->assertSee('Pod B');
    }

    public function test_manager_only_sees_managed_pods(): void
    {
        $this->actingAs($this->manager)->get('/dashboard')
            ->assertOk()->assertSee('Pod A')->assertDontSee('Pod B');
    }

    public function test_only_super_admin_can_view_reports(): void
    {
        $this->actingAs($this->manager)->get('/reports')->assertForbidden();
        $this->actingAs($this->admin)->get('/reports')->assertOk();
    }

    public function test_only_super_admin_can_download_report_pdf(): void
    {
        $this->actingAs($this->manager)->get('/reports/pdf')->assertForbidden();
        $this->actingAs($this->admin)->get('/reports/pdf')
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_manager_cannot_add_client_to_unmanaged_pod(): void
    {
        $this->actingAs($this->manager)
            ->post('/clients', ['pod_id' => $this->podB->id, 'name' => 'Intruder'])
            ->assertForbidden();

        $this->assertDatabaseMissing('clients', ['name' => 'Intruder']);
    }

    public function test_manager_can_add_client_to_own_pod(): void
    {
        $this->actingAs($this->manager)
            ->post('/clients', ['pod_id' => $this->podA->id, 'name' => 'New Client', 'industry' => 'Retail'])
            ->assertRedirect();

        $this->assertDatabaseHas('clients', ['name' => 'New Client', 'pod_id' => $this->podA->id]);
    }

    public function test_section_text_can_be_updated(): void
    {
        $this->actingAs($this->manager)
            ->put('/sections/'.$this->sectionA->id, ['content' => 'Important notes'])
            ->assertRedirect();

        $this->assertDatabaseHas('goal_sections', ['id' => $this->sectionA->id, 'content' => 'Important notes']);
    }

    public function test_file_upload_stores_and_is_downloadable(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('brief.pdf', 12, 'application/pdf');

        $this->actingAs($this->manager)
            ->post('/sections/'.$this->sectionA->id.'/attachments', ['file' => $file])
            ->assertRedirect();

        $attachment = Attachment::first();
        $this->assertNotNull($attachment);
        $this->assertSame('pdf', $attachment->file_type);
        Storage::disk('local')->assertExists($attachment->stored_path);

        $this->actingAs($this->manager)->get('/attachments/'.$attachment->id.'/download')->assertOk();
    }

    public function test_manager_cannot_upload_to_unmanaged_pods_section(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('x.pdf', 5, 'application/pdf');

        $this->actingAs($this->otherManager)
            ->post('/sections/'.$this->sectionA->id.'/attachments', ['file' => $file])
            ->assertForbidden();

        $this->assertDatabaseCount('attachments', 0);
    }

    public function test_weekly_toggle_persists(): void
    {
        $task = $this->clientA->weeklyTasks()->create([
            'task' => 'Ship it', 'status' => 'pending', 'week_start' => '2025-06-09',
        ]);

        $this->actingAs($this->manager)->patch('/weekly-tasks/'.$task->id.'/toggle')->assertRedirect();

        $this->assertDatabaseHas('weekly_tasks', ['id' => $task->id, 'status' => 'done']);
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }
}
