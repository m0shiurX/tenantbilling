<?php

namespace App\Http\Controllers\Settings;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreUserRequest;
use App\Http\Requests\Settings\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $this->authorize('user_access');

        $users = User::query()
            ->with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->role, function ($query, $role) {
                $query->whereHas('roles', fn ($q) => $q->where('name', $role));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('settings/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'status', 'role']),
            'statuses' => UserStatus::options(),
            'roles' => Role::pluck('name'),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        $this->authorize('user_create');

        return Inertia::render('settings/users/Create', [
            'roles' => Role::all(['id', 'name']),
            'statuses' => UserStatus::options(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('user_create');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'status' => $request->status ?? UserStatus::Active,
        ]);

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        $this->authorize('user_edit');

        return Inertia::render('settings/users/Edit', [
            'user' => $user->load('roles'),
            'roles' => Role::all(['id', 'name']),
            'statuses' => UserStatus::options(),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('user_edit');

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('user_delete');

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deletion of super admin
        if ($user->hasRole('Super Admin')) {
            return back()->with('error', 'Cannot delete Super Admin user.');
        }

        $user->delete();

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
