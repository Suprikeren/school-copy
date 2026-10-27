<?php

namespace App\Http\Controllers\Dashboard\Spatie;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //

    public function index(Request $request)
    {
        // request page data
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        // get all user data
        $users = User::query()
            ->with(['roles' => function ($query) {
                $query->with(['permissions' => function ($query) {
                    $query->select('id', 'name');
                }])->select('id', 'name');
            }])
            ->select('id', 'name', 'email')
            ->when($request->search, fn ($search) => $search->where('name', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate($perPage, ['*'], 'page', $currentPage)->withQueryString();

        // render view
        return inertia('dashboard/users/index', [
            'users' => $users,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // get all role data
        $roles = Role::query()
            ->with('permissions')
            ->select('id', 'name')
            // ->where('name', '!=', 'super-admin')
            ->get();

        // render view
        return inertia('dashboard/users/create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        // create new user data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // assign role to user
        $user->assignRole($request->selectedRoles);

        // render view
        return to_route('dashboard.users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // load relationship
        $user->load('roles', 'roles.permissions');

        // render view
        return inertia('dashboard/users/show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // get all role data
        $roles = Role::query()
            ->with('permissions')
            ->select('id', 'name')
            // ->where('name', '!=', 'super-admin')
            ->get();

        // load relationship
        $user->load(['roles' => fn ($query) => $query->select('id', 'name'), 'roles.permissions' => fn ($query) => $query->select('id', 'name')]);

        // render view
        return inertia('dashboard/users/edit', [
            'roles' => $roles,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        // check if user send request password
        if ($request->password) {
            // update user data password
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        // update user data name
        $user->update([
            'username' => $request->username,
            'name' => $request->name,
        ]);

        // assign role to user
        $user->syncRoles($request->selectedRoles);

        // render view
        return to_route('dashboard.users.index')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // delete user data
        $user->delete();

        // render view
        return back()->with('success', 'User berhasil dihapus!');
    }
}
