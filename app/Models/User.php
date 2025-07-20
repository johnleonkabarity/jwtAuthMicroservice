<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // <-- 1. Importar el trait

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; // Importar el contrato

class User extends Authenticatable implements JwtSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'is_system_admin',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

  /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Usamos el ID del usuario como identificador
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
   public function getJWTCustomClaims()
{
    // Cargar relaciones necesarias de una vez para evitar N+1 queries
    $this->loadMissing('roles.permissions', 'directPermissions');

    // Obtener permisos directos
    $directPermissions = $this->directPermissions->pluck('name');

    // Obtener permisos a través de roles
    $permissionsFromRoles = $this->roles->flatMap(function ($role) {
        return $role->permissions->pluck('name');
    });

    // Unir todos los permisos y eliminar duplicados
    $allPermissions = $directPermissions->merge($permissionsFromRoles)->unique()->values();

    // Obtener nombres de los roles
    $roleNames = $this->roles->pluck('name');

    // Construir el payload del JWT
    return [
        'user_id'         => $this->id,
        'email'           => $this->email,
        'name'            => $this->name,
        'is_system_admin' => (bool) $this->is_system_admin,
        'roles'           => $roleNames,
        'permissions'     => $allPermissions,
    ];
}


    public function roles(){
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function rolesWithPermissions(){
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')->with('permissions');
    }

    public function directPermissions(){
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id');
    }

    public function directObjectPermissions(){
        return $this->belongsToMany(Permission::class, 'object_permission_user', 'user_id', 'permission_id')
        ->withPivot('object_type', 'object_id')->groupBy('object_type');
    }
}
