<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que el payload del JWT se construye correctamente con roles y permisos.
     *
     * @return void
     */
    public function test_jwt_custom_claims_are_generated_correctly()
    {
        // 1. Arrange: Crear el entorno
        $user = User::factory()->create(['is_system_admin' => false]);
        
        // Modificado: Se usa create() en lugar de factory()
        $role = Role::create(['name' => 'editor']);
        $directPermission = Permission::create(['name' => 'publish-articles']);
        $rolePermission = Permission::create(['name' => 'edit-articles']);

        // Asignar rol y permisos
        $user->roles()->attach($role);
        $user->directPermissions()->attach($directPermission);
        $role->permissions()->attach($rolePermission);

        // 2. Act: Ejecutar la lógica a probar
        $claims = $user->getJWTCustomClaims();

        // 3. Assert: Verificar los resultados
        $this->assertArrayHasKey('user_id', $claims);
        $this->assertEquals($user->id, $claims['user_id']);
        $this->assertFalse($claims['is_system_admin']);

        $this->assertArrayHasKey('roles', $claims);
        $this->assertContains('editor', $claims['roles']);

        $this->assertArrayHasKey('permissions', $claims);
        $this->assertCount(2, $claims['permissions']); // Permiso directo + permiso de rol
        $this->assertContains('publish-articles', $claims['permissions']);
        $this->assertContains('edit-articles', $claims['permissions']);
    }
}
