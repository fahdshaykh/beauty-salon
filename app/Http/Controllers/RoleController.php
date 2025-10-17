<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Role;

use App\RolePermissions;

use DB;

use App\Models\Setting;
use App\Models\UserSetting;



class RoleController extends Controller

{

    public function index(Request $request)
    {
        $paginationOptions = Setting::getPaginationOptions();
        $perPage = $request->input('per_page', auth()->user()->getPerPagePreference());
        if (!in_array($perPage, $paginationOptions)) {
            $perPage = auth()->user()->getPerPagePreference();
        }
        if ($request->has('per_page')) {
            if (auth()->user()->setting) {
                auth()->user()->setting->update(['per_page' => $perPage]);
            } else {
                UserSetting::create(['user_id' => auth()->user()->id, 'per_page' => $perPage]);
            }
        }

        $roles = DB::table('roles')->where('company_id', auth()->user()->company_id)
        ->paginate($perPage)->appends(['per_page' => $perPage]);

        $role_permissions = RolePermissions::select('role_id')->where('company_id', auth()->user()->company_id)->groupBy('role_id')
        ->paginate($perPage)->appends(['per_page' => $perPage]);

        return view('roles.index',compact('roles', 'role_permissions','paginationOptions'));

    }



    public function create()

    {

        $permissions_roles = DB::table('role_permissions')

        ->select('role_permissions.*','permissions.name')

        ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')

        ->where('role_permissions.role_id', 1)

        ->get();

        $permissions = $permissions_roles->groupBy('permission_id');

        // dd($permissions);

        return view('roles.create',compact('permissions'));

    }



    public function store(Request $request)

    {

	$this->validate($request, 



            [



                'role' => 'required',



            ],



            [



                'role.required' => 'Please enter Role name',



            ],



        );



        $data = $request->all();

        

        $role = Role::create([

            'role' => $data['role'],

            'company_id' => auth()->user()->company_id,

        ]);



	if(!$request->access_id){

		return back()->with('validate', 'Please select permissions for this role.');

	}



        foreach($data['access_id'] as $key => $value){

            foreach($value as $val){

                $insert_data = array (

                    'role_id' => $role->id,

                    'permission_id' => $key,

                    'access_id' => $val,

                );

                $insert_data['company_id'] = auth()->user()->company_id;

                RolePermissions::create($insert_data);

                // DB::table('role_permissions')->updateOrInsert($insert_data);

            }

        }



        return redirect('roles')->with('success','Role created successfully');

    }



    public function edit($id)

    {

        $role = DB::table('roles')->select('id','role')

            ->where('id',$id)

            ->where('company_id', auth()->user()->company_id)

            ->get();

        

            $permissions_roles = DB::table('role_permissions')

            ->select('role_permissions.*','permissions.name')

            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')

            ->where('role_permissions.role_id', 1)

            ->get();

            $permissions = $permissions_roles->groupBy('permission_id');



        $role_permissions_data = DB::table('role_permissions')->where('company_id', auth()->user()->company_id)->where('role_id',$id)->get();

        $grouped_data = $role_permissions_data->groupBy('permission_id');

        //    echo "<pre>"; print_r($role_permissions_data); exit;

        // $role_grouped = $grouped_data->groupBy('role_id');

         //echo "<pre>";print_r($grouped_data);



        $new_array = array();

        foreach($grouped_data as $accessKey => $permissionValue)

        {

            foreach($permissionValue as $valued)

            {

                $new_array["permissions"][$accessKey][] =  $valued->access_id;               

            }

            

        }



        return view('roles.edit',compact('new_array','permissions','role'));

    }



    public function update(Request $request, $id)

    {

        // dd($id);

        $data = $request->all();

        $role = Role::where('id', $id)->where('company_id', auth()->user()->company_id)

            ->update(['role' => $data['role']]);



        $deletedRows = RolePermissions::where('role_id', $id)->where('company_id', auth()->user()->company_id)->delete();

        foreach($data['access_id'] as $key => $value){

            foreach($value as $val){

                $insert_data = array (

                    'role_id' => $id,

                    'permission_id' => $key,

                    'access_id' => $val,

                );

                $insert_data['company_id'] = auth()->user()->company_id;

                RolePermissions::create($insert_data);

                // DB::table('role_permissions')->updateOrInsert($insert_data);

            }

        }

        return redirect('roles')->with('success','Role Updated successfully');

    }



    public function destroy($id)

    {

        DB::table('roles')

            ->where('id', $id)

            ->where('company_id', auth()->user()->company_id)

            ->update(['is_deleted' => 1]);

        $deletedRows = RolePermissions::where('role_id', $id)->where('company_id', auth()->user()->company_id)->delete();

        return back()->with('destroy','Role deleted successfully');



    }

}

