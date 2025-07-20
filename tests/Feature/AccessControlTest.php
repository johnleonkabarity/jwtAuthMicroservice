<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB; // Importación añadida
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario administrador y uno normal para las pruebas
        $this->adminUser = User::factory()->create(['is_system_admin' => true]);
        $this->regularUser = User::factory()->create(['is_system_admin' => false]);
    }

    public function test_admin_can_create_a_role()
    {
        $response = $this->actingAs($this->adminUser, 'api')
            ->postJson('/api/admin/access-control/roles', [
                'name' => 'moderator',
                'description' => 'Can moderate comments',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('roles', ['name' => 'moderator']);
    }

    public function test_regular_user_cannot_create_a_role()
    {
        $response = $this->actingAs($this->regularUser, 'api')
            ->postJson('/api/admin/access-control/roles', ['name' => 'hacker']);

        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseMissing('roles', ['name' => 'hacker']);
    }

    public function test_admin_can_assign_a_role_to_a_user()
    {
        // Modificado: Se usa create() en lugar de factory()
        $role = Role::create(['name' => 'editor']);

        $response = $this->actingAs($this->adminUser, 'api')
            ->postJson('/api/admin/access-control/assign/role-to-user', [
                'user_id' => $this->regularUser->id,
                'role_name' => 'editor',
            ]);

        $response->assertStatus(200);
        // Recargamos el modelo para obtener la relación actualizada
        $this->regularUser->load('roles');
        $this->assertTrue($this->regularUser->roles->contains($role));
    }

    public function test_admin_can_view_any_users_access_details()
    {
        // Modificado: Se usa create() en lugar de factory()
        $role = Role::create(['name' => 'test-role-'.uniqid()]);
        $this->regularUser->roles()->attach($role);

        $response = $this->actingAs($this->adminUser, 'api')
            ->getJson("/api/admin/access-control/users/{$this->regularUser->id}/access");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $role->name]);
    }

    public function test_regular_user_can_view_their_own_access()
    {
        // Modificado: Se usa create() en lugar de factory()
        $role = Role::create(['name' => 'viewer']);
        $this->regularUser->roles()->attach($role);

        $response = $this->actingAs($this->regularUser, 'api')
            ->getJson('/api/me/access');

        $response->assertStatus(200)
            ->assertJson([
                'roles' => ['viewer'],
            ]);
    }

    public function test_user_can_check_object_permission()
    {
        // Modificado: Se usa create() en lugar de factory()
        $permission = Permission::create(['name' => 'edit-post']);
        
        // Asignar permiso de objeto
        DB::table('object_permission_user')->insert([
            'user_id' => $this->regularUser->id,
            'permission_id' => $permission->id,
            'object_type' => 'App\\Models\\Post',
            'object_id' => '123',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->regularUser, 'api')
            ->postJson('/api/me/access/check-object-permission', [
                'permission_name' => 'edit-post',
                'object_type' => 'App\\Models\\Post',
                'object_id' => '123',
            ]);

        $response->assertStatus(200)
            ->assertJson(['has_permission' => true]);
    }
}
