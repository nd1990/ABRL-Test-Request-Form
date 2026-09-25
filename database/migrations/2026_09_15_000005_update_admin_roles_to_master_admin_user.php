<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First widen the column so MySQL accepts the new values.
        Schema::table('admins', function (Blueprint $table) {
            $table->string('role', 50)->default('master_admin')->change();
        });

        // Historic values: 'master' (full access) and 'admin' (logged-in user).
        DB::table('admins')->where('role', 'master')->update(['role' => 'master_admin']);
        DB::table('admins')->where('role', 'admin')->update(['role' => 'user']);

        // Preserve existing full access: grant every assignable permission to
        // previously 'admin' accounts so nobody is locked out after the change.
        $permissions = config('permissions.assignable', []);
        $convertedUserIds = DB::table('admins')->where('role', 'user')->pluck('id');
        foreach ($convertedUserIds as $adminId) {
            foreach ($permissions as $permission) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => $adminId, 'permission' => $permission],
                    ['permission' => $permission, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Narrow back to the new allowed values.
        Schema::table('admins', function (Blueprint $table) {
            $table->enum('role', ['master_admin', 'user'])->default('master_admin')->change();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->enum('role', ['master', 'admin'])->default('master')->change();
        });

        DB::table('admins')->where('role', 'master_admin')->update(['role' => 'master']);
        DB::table('admins')->where('role', 'user')->update(['role' => 'admin']);
    }
};