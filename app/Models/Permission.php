<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Permission extends Model
{
    use HasUuids;

    public $fillable = [
        'name',
        'description'
    ];

    public function roles(){
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'permission_user', 'permission_id', 'user_id');
    }
}
