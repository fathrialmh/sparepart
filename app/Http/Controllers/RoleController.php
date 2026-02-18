<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('permissions')->latest('id')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('role.index', compact('roles', 'permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name'],
            'guard_name'    => ['nullable', 'string', 'max:255'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return redirect()->back()->with('success', 'Role berhasil dibuat.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'guard_name'    => ['nullable', 'string', 'max:255'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? $role->guard_name,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->back()->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->permissions()->detach();
        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }
}
