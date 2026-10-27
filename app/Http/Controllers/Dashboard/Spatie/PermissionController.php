<?php

namespace App\Http\Controllers\Dashboard\Spatie;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller implements HasMiddleware
{
  public static function middleware(): array
{
    return [
        new Middleware(PermissionMiddleware::using('permissions index'), only: ['index']),
        new Middleware(PermissionMiddleware::using('permissions store'), only: ['store']),
        new Middleware(PermissionMiddleware::using('permissions edit'), only: ['edit']),
        new Middleware(PermissionMiddleware::using('permissions show'), only: ['show']),
        new Middleware(PermissionMiddleware::using('permissions delete'), only: ['destroy']),
    ];
}

    //
    public function index(Request $request)
    {
        $permissions = Permission::query()
            ->select('id', 'name')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('dashboard/permissions/index', [
            'permissions' => $permissions,
            'filters' => $request->only(['search']), // Tambahkan ini
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request)
    {
        // create new permission
        Permission::create(['name' => $request->name]);

        // render view
        return back()->with('success', 'Hak akses baru berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        // update permission data
        $permission->update(['name' => $request->name]);

        // render view
        return back()->with('success', 'Hak akses berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        // delete permission data
        $permission->delete();

        // render view
        return back()->with('success', 'Hak akses berhasil dihapus!');

    }
}
