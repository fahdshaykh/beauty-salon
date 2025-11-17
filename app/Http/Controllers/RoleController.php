<?php



namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

use DB;

use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller

{

    /**
     * Restrict all user management routes to admin users only.
     * Using a route middleware closure so we don't rely on route definitions.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! (auth()->user() && auth()->user()->hasRole('admin'))) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // get rows per page if passed with request
        if (request('rows')) {

            $rows = 500 < intval(request('rows')) ? 500 : intval(request('rows'));
            $rows = 5 > $rows ? 5 : $rows;
            Auth::user()->setSettings(['roles_rows_per_page' => $rows]);
        }
        // otherwise retrieve from user's settings
        $rows = Auth::user()->getSetting('roles_rows_per_page', auth()->user()->default_rows);

        $roles = Role::where('company_id', auth()->user()->company_id)
                 ->with('permissions')
                 ->paginate($rows);



        return view('roles.index',compact('roles','rows'));

    }



    public function create()
    {
        // Get ALL permissions (global, not per-role)
        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('_', $perm->name)[0]; // Group by module: view, create, etc.
        });

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,NULL,id,company_id,' . auth()->user()->company_id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'Please enter role name',
            'name.unique' => 'This role already exists in your company',
            'permissions.required' => 'Please select at least one permission'
        ]);

        // CREATE ROLE (Your custom model)
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'company_id' => auth()->user()->company_id,
        ]);

        // SET TEAM ID (CRITICAL!)
        app(PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->company_id);

        // SYNC PERMISSIONS
        $role->syncPermissions($request->permissions);

        // Clear Spatie permission cache so users immediately see updated permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }



    public function edit(Role $role)
    {
        // Security: Only allow editing roles from same company
        // if ($role->company_id !== auth()->user()->company_id) {
        //     abort(403);
        // }

        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('_', $perm->name)[0]; // Group by module
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        // Security
        if ($role->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . ',id,company_id,' . auth()->user()->company_id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'Please enter role name',
            'name.unique' => 'This role name already exists',
            'permissions.required' => 'Please select at least one permission'
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        // SET TEAM ID
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->company_id);

        // SYNC PERMISSIONS
        $role->syncPermissions($request->permissions);

    // Clear Spatie permission cache so users immediately see updated permissions
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('update', 'Role updated successfully');
    }



    public function destroy(Role $role)
    {
        // SECURITY: Only allow delete from same company
        if ($role->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // PREVENT DELETING 'admin' ROLE
        if ($role->name === 'admin') {
            return back()->with('destroy', 'Cannot delete the admin role.');
        }

        // CHECK IF ANY USER HAS THIS ROLE
        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return back()->with('destroy', "Cannot delete role. It is assigned to {$userCount} user(s).");
        }

        // SET TEAM ID
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->company_id);

        // DELETE ROLE + PERMISSIONS
        $role->delete();

    // Clear cached permissions to reflect deletion
    app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('destroy', 'Role deleted successfully');
    }

}

