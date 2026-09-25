<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function index()
    {
        $users = Admin::with('permissions')
            ->where('is_hidden', false)
            ->latest()
            ->paginate(20);

        $modalUsers = $users->map(fn (Admin $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_master' => $user->isMaster(),
            'permissions' => $user->permissionList(),
        ])->values();

        return view('admin.users.index', [
            'users' => $users,
            'modalUsers' => $modalUsers,
            'modules' => config('permissions.modules'),
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'modules' => config('permissions.modules'),
        ]);
    }

    public function edit(Admin $user)
    {
        // Master admins are managed only via "Settings > Change Password".
        if ($user->isMaster()) {
            return redirect()->route('admin.users.index')->with('error', 'The master admin cannot be edited here. Use "Settings > Change Password" instead.');
        }

        $user->load('permissions');

        return view('admin.users.edit', [
            'user' => $user,
            'modules' => config('permissions.modules'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateUser($request);

        $user = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (!$user->isMaster()) {
            $user->syncPermissions($data['permissions'] ?? []);
        }

        $this->auditLog->log(app('admin'), 'admin_user.created', 'Admin', $user->id, null, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'permissions' => $user->permissionList(),
        ], $request);

        return redirect()->route('admin.users.index')->with('success', 'User "' . $user->name . '" created successfully.');
    }

    public function update(Request $request, Admin $user)
    {
        // Guard: never allow editing the master admin via this page
        if ($user->isMaster()) {
            return back()->with('error', 'The master admin cannot be edited here. Use "Settings > Change Password" instead.');
        }

        $data = $this->validateUser($request, $user->id);
        $old = $user->toArray();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if ($user->isMaster()) {
            $user->permissions()->delete();
        } else {
            $user->syncPermissions($data['permissions'] ?? []);
        }

        $this->auditLog->log(app('admin'), 'admin_user.updated', 'Admin', $user->id, $old, $user->toArray() + ['permissions' => $user->permissionList()], $request);

        return redirect()->route('admin.users.index')->with('success', 'User "' . $user->name . '" updated successfully.');
    }

    public function destroy(Request $request, Admin $user)
    {
        // Guard: master admin cannot be deleted
        if ($user->isMaster()) {
            return back()->with('error', 'The master admin cannot be deleted.');
        }

        // Prevent deleting yourself
        if ($user->id === (int) session('admin_id')) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $this->auditLog->log(app('admin'), 'admin_user.deleted', 'Admin', $user->id, $user->toArray(), null, $request);

        $name = $user->name;
        $user->delete();

        return back()->with('success', 'User "' . $name . '" deleted successfully.');
    }

    public function changePassword(Request $request)
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $admin = Admin::findOrFail(session('admin_id'));

        if (!Hash::check($data['current_password'], $admin->password)) {
            return redirect()->route('admin.settings.index')
                ->withErrors(['current_password' => 'Your current password is incorrect.'], 'password');
        }

        $old = $admin->toArray();
        $admin->update(['password' => Hash::make($data['password'])]);

        $this->auditLog->log($admin, 'admin.password_changed', 'Admin', $admin->id, $old, ['password_changed' => true], $request);

        return redirect()->route('admin.settings.index')->with('pw_success', 'Your password has been changed successfully.');
    }

    protected function validateUser(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', Rule::unique('admins', 'email')->ignore($ignoreId)],
            'role' => ['required', Rule::in([Admin::ROLE_MASTER, Admin::ROLE_USER])],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::in(config('permissions.assignable'))],
            'password' => [$ignoreId === null ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($data['role'] !== Admin::ROLE_MASTER && empty(array_filter($data['permissions'] ?? []))) {
            throw ValidationException::withMessages([
                'permissions' => 'Select at least one permission for a User-role account.',
            ]);
        }

        return $data;
    }
}