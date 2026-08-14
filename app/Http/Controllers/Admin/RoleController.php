<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\AdminPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

final class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()->withCount('users')->orderBy('name')->get(),
            'permissions' => AdminPermission::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', ['permissions' => AdminPermission::cases()]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name']);
        $role = Role::query()->create($data);

        return redirect()->route('admin.roles.edit', $role)->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        abort_if($role->isProtected(), 403, 'This system role cannot be edited.');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => AdminPermission::cases(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        abort_if($role->isProtected(), 403, 'This system role cannot be edited.');
        $role->update($request->validated());

        return back()->with('success', 'Role permissions updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->is_system, 403, 'System roles cannot be deleted.');

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => 'Move all staff members to another role before deleting this role.']);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (Role::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
