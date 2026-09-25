<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_MASTER = 'master_admin';
    public const ROLE_USER = 'user';

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'is_hidden', 'last_login_at'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed', 'last_login_at' => 'datetime', 'is_active' => 'boolean', 'is_hidden' => 'boolean'];

    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }

    public function isAdmin(): bool { return $this->role === self::ROLE_USER; }
    public function isMaster(): bool { return $this->role === self::ROLE_MASTER; }

    public function roleLabel(): string
    {
        return $this->isMaster() ? 'Master Admin' : 'User';
    }

    /**
     * Resolve the stored permission keys for this admin (empty for master).
     */
    public function permissionList(): array
    {
        if (!$this->relationLoaded('permissions')) {
            $this->load('permissions');
        }

        return $this->permissions->pluck('permission')->all();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->isMaster() || in_array($permission, $this->permissionList(), true);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isMaster()) {
            return true;
        }

        $granted = $this->permissionList();

        foreach ($permissions as $permission) {
            if (in_array($permission, $granted, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * How many modules this admin has at least one permission for.
     */
    public function moduleCount(): int
    {
        if ($this->isMaster()) {
            return count(config('permissions.modules'));
        }

        $granted = $this->permissionList();
        $count = 0;

        foreach (array_keys(config('permissions.modules')) as $module) {
            foreach (config("permissions.modules.$module.permissions") as $permission => $label) {
                if (in_array($permission, $granted, true)) {
                    $count++;
                    break;
                }
            }
        }

        return $count;
    }

    /**
     * Replace this admin's stored permissions with the given list.
     */
    public function syncPermissions(array $permissions): void
    {
        $allowed = collect(config('permissions.assignable'))->flip();
        $keys = collect($permissions)
            ->filter(fn ($p) => is_string($p) && $allowed->has($p))
            ->unique()
            ->values();

        $this->permissions()->delete();

        foreach ($keys as $key) {
            $this->permissions()->create(['permission' => $key]);
        }
    }
}