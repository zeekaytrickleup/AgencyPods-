<?php

namespace Tests\Feature;

use App\Models\Pod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $manager;       // manages podA
    private User $otherManager;  // manages podB
    private User $teamMember;    // reports to $manager
    private Pod $podA;
    private Pod $podB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->makeUser('admin@t.com', 'super_admin');
        $this->manager = $this->makeUser('m1@t.com', 'pod_manager');
        $this->otherManager = $this->makeUser('m2@t.com', 'pod_manager');
        $this->teamMember = $this->makeUser('tm@t.com', 'team_member', $this->manager->id);

        $this->podA = Pod::create(['name' => 'Pod A', 'color' => '#111111', 'manager_id' => $this->manager->id]);
        $this->podB = Pod::create(['name' => 'Pod B', 'color' => '#222222', 'manager_id' => $this->otherManager->id]);
    }

    private function makeUser(string $email, string $role, ?int $managerId = null): User
    {
        return User::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'manager_id' => $managerId,
        ]);
    }

    // ----- Super admin: user management -----

    public function test_super_admin_can_view_users_page(): void
    {
        $this->actingAs($this->admin)->get('/users')->assertOk()->assertSee('Pod Manager');
    }

    public function test_non_admins_cannot_view_users_page(): void
    {
        $this->actingAs($this->manager)->get('/users')->assertForbidden();
        $this->actingAs($this->teamMember)->get('/users')->assertForbidden();
    }

    public function test_super_admin_can_create_a_manager_and_assign_pods(): void
    {
        $this->actingAs($this->admin)->post('/users', [
            'name' => 'New Manager',
            'email' => 'new@t.com',
            'password' => 'password123',
            'role' => 'pod_manager',
            'pods' => [$this->podB->id],
        ])->assertRedirect('/users');

        $new = User::where('email', 'new@t.com')->first();
        $this->assertNotNull($new);
        $this->assertSame('pod_manager', $new->role);
        $this->assertSame($new->id, $this->podB->fresh()->manager_id);
    }

    public function test_super_admin_can_create_a_pod(): void
    {
        $this->actingAs($this->admin)->post('/pods', [
            'name' => 'Pod Z',
            'color' => '#FCD82F',
            'manager_id' => $this->manager->id,
        ])->assertRedirect('/users');

        $this->assertDatabaseHas('pods', ['name' => 'Pod Z', 'manager_id' => $this->manager->id]);
    }

    // ----- Manager: team management -----

    public function test_manager_can_add_a_team_member(): void
    {
        $this->actingAs($this->manager)->post('/team', [
            'name' => 'Teammate',
            'email' => 'mate@t.com',
            'password' => 'password123',
        ])->assertRedirect('/team');

        $this->assertDatabaseHas('users', [
            'email' => 'mate@t.com',
            'role' => 'team_member',
            'manager_id' => $this->manager->id,
        ]);
    }

    public function test_manager_sees_team_page_but_not_users_page(): void
    {
        $this->actingAs($this->manager)->get('/team')->assertOk();
        $this->actingAs($this->manager)->get('/users')->assertForbidden();
    }

    public function test_manager_cannot_remove_another_managers_team_member(): void
    {
        $foreign = $this->makeUser('foreign@t.com', 'team_member', $this->otherManager->id);

        $this->actingAs($this->manager)->delete('/team/'.$foreign->id)->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $foreign->id]);
    }

    // ----- Team member access -----

    public function test_team_member_sees_only_their_managers_pods(): void
    {
        $this->actingAs($this->teamMember)->get('/dashboard')
            ->assertOk()->assertSee('Pod A')->assertDontSee('Pod B');
    }

    public function test_team_member_cannot_manage_users_or_team(): void
    {
        $this->actingAs($this->teamMember)->get('/users')->assertForbidden();
        $this->actingAs($this->teamMember)->get('/team')->assertForbidden();
    }
}
