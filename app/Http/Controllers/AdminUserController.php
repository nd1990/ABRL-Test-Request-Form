<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function index()
    {
        $users = Admin::where('is_hidden', false)->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function showChangePassword()
    {
        return view('admin.users.change-password');
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

        $this->auditLog->log(app('admin'), 'admin_user.created', 'Admin', $user->id, null, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ], $request);

        return redirect()->route('admin.users.index')->with('success', 'User "' . $user->name . '" created successfully.');
    }

    public function update(Request $request, Admin $user)
    {
        // Guard: never allow editing the master admin via this page
        if ($user->role === 'master') {
            return back()->with('error', 'The master admin cannot be edited here. Use "Change Password" instead.');
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

        $this->auditLog->log(app('admin'), 'admin_user.updated', 'Admin', $user->id, $old, $user->toArray(), $request);

        return redirect()->route('admin.users.index')->with('success', 'User "' . $user->name . '" updated successfully.');
    }

    public function destroy(Request $request, Admin $user)
    {
        // Guard: master admin cannot be deleted
        if ($user->role === 'master') {
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
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $admin = Admin::findOrFail(session('admin_id'));

        if (!Hash::check($data['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.'])->withInput();
        }

        $old = $admin->toArray();
        $admin->update(['password' => Hash::make($data['password'])]);

        $this->auditLog->log($admin, 'admin.password_changed', 'Admin', $admin->id, $old, ['password_changed' => true], $request);

        return back()->with('success', 'Your password has been changed successfully.');
    }

    protected function validateUser(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', Rule::unique('admins', 'email')->ignore($ignoreId)],
            'role' => ['required', 'in:admin,master'],
            'password' => [$ignoreId === null ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
