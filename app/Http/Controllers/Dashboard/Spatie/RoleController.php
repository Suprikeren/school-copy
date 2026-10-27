<?php

namespace App\Http\Controllers\Dashboard\Spatie;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    //
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('roles index'), only: ['index']),
            new Middleware(PermissionMiddleware::using('roles store'), only: ['store']),
            new Middleware(PermissionMiddleware::using('roles edit'), only: ['edit']),
            new Middleware(PermissionMiddleware::using('roles show'), only: ['show']),
            new Middleware(PermissionMiddleware::using('roles delete'), only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        //
        // request page data
        $currentPage = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        // get all role data
        $roles = Role::query()
            ->with('permissions')
            ->select('id', 'name')
            // ->where('name', '!=', 'super-admin')
            ->when($request->search, fn ($search) => $search->where('name', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate($perPage, ['*'], 'page', $currentPage)->withQueryString();

        // render view
        return inertia('dashboard/roles/index', [
            'roles' => $roles,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $permissions = Permission::query()->select('id', 'name')->get();

        // render view
        return inertia('dashboard/roles/create', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        //
        // create new role
        $role = Role::create(['name' => $request->name]);

        // assign permission to role
        $role->givePermissionTo($request->selectedPermissions);

        // render view
        return to_route('dashboard.roles.index')->with('success', 'Role baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
        $permissions = Permission::query()->select('id', 'name')->get();

        // load relationship
        $role->load('permissions');

        // render view
        return inertia('dashboard/roles/edit', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //
        // update role data name
        $role->update(['name' => $request->name]);

        // assign permission to role
        $role->syncPermissions($request->selectedPermissions);

        // render view
        return to_route('dashboard.roles.index')->with('success', 'Role berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
        // delete role data
        $role->delete();

        // render view
        return back()->with('success', 'Role berhasil dihapus!');
    }
}
