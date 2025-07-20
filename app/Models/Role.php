<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasUuids;
    public $fillable = [
        'name',
        'description'
    ];

    public function permissions(){
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}
