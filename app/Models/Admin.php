<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'is_hidden', 'last_login_at'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed', 'last_login_at' => 'datetime', 'is_active' => 'boolean', 'is_hidden' => 'boolean'];

    public function isAdmin() { return $this->role === 'admin'; }
    public function isMaster() { return $this->role === 'master'; }
}
