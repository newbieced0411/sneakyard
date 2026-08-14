<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->staff()
            ->with('accessRole')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($request->filled('role'), fn ($query) => $query->whereHas(
                'accessRole',
                fn ($query) => $query->where('slug', $request->string('role')),
            ))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $this->staffRoles(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['roles' => $this->staffRoles()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::query()->findOrFail($data['role_id']);
        $data['role'] = $role->slug;
        $user = User::query()->create($data);

        return redirect()->route('admin.users.edit', $user)->with('success', 'Staff user created successfully.');
    }

    public function edit(User $user): View
    {
        $this->ensureStaff($user);

        return view('admin.users.edit', [
            'managedUser' => $user->load('accessRole'),
            'roles' => $this->staffRoles(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->ensureStaff($user);
        $data = $request->validated();

        if ($request->user()->is($user)) {
            unset($data['role_id'], $data['is_active']);
        } else {
            $data['role'] = Role::query()->findOrFail($data['role_id'])->slug;
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Staff user updated successfully.');
    }

    private function ensureStaff(User $user): void
    {
        $roleSlug = $user->accessRole?->slug ?? $user->role;

        abort_if($roleSlug === 'customer', 404);
    }

    /** @return Collection<int, Role> */
    private function staffRoles(): Collection
    {
        return Role::query()->where('slug', '!=', 'customer')->orderBy('name')->get();
    }
}
