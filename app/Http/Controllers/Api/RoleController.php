<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\Role;
use App\RolePermissions;
use DB;
use Validator;

class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [];
        $roles = DB::table('roles')->where('company_id', $request->company_id)->get();
        $role_permissions = RolePermissions::select('role_id','permission_id','access_id','company_id')->where('company_id', $request->company_id)->get(); //->groupBy('role_id')

        $data['roles'] = $roles;
        $data['role_permissions'] = $role_permissions;
        
        if (count($data) == 0) {
            return $this->sendResponse($data, 'Roles and permissions not found.');
        }

        return $this->sendResponse($data, 'Roles and permissions retrieved successfully.');
    }

    public function permissions_name()
    {
        $permissions = DB::table('permissions')->select('id','name')->get();

        // $permissions_roles = DB::table('role_permissions')
                //     ->select('role_permissions.permission_id','role_permissions.access_id','permissions.name')
                //     ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                //     ->where('role_permissions.role_id', 1)
                //     ->get();
        // $permissions = $permissions_roles->groupBy('permission_id');

        if (count($permissions) == 0) {
            return $this->sendResponse($permissions, 'Roles and permissions not found.');
        }

        return $this->sendResponse($permissions, 'Permissions retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions_roles = DB::table('role_permissions')
        ->select('role_permissions.*','permissions.name')
        ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
        ->where('role_permissions.role_id', 1)
        ->get();

        $permissions = $permissions_roles->groupBy('permission_id');

        $all_permissions = [];

        foreach($permissions as $key => $permission){
            $value_array = [];
            $permission_array = array(
                'permission_id' => $key
            );

            array_push($value_array,$permission_array);

            foreach($permission as $access){
                $access_array = array(
                    'access_id' => $access->access_id
                );
                
                array_push($value_array,$access_array);
                
            }

            array_push($all_permissions, $value_array);
            
        }

        $result = json_encode($all_permissions);

        echo $result;

        exit;

        // return $this->sendResponse($permissions->toArray(), 'Roles and permissions retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['access_id'] = json_decode($data['access_id'], TRUE);

        $validator = Validator::make($data, [
            'role' => 'required',
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $role = Role::create([
            'role' => $data['role'],
            'company_id' => $request->company_id,
        ]);

        foreach($data['access_id'] as $key => $value){
            foreach($value as $val){
                $insert_data = array (
                    'role_id' => $role->id,
                    'permission_id' => $key,
                    'access_id' => $val,
                );
                $insert_data['company_id'] = $request->company_id;
                $data = RolePermissions::create($insert_data);
                
            }
        }
        return $this->sendResponse($role->toArray(), 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'id' => 'required',
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $data = [];

        $role = DB::table('roles')->select('id','role')
            ->where('id',$request->id)
            ->where('company_id', $request->company_id)
            ->get();

            if($role->isEmpty()){
                return $this->sendResponse($role, 'Something went wrong.');
            }
        
            $permissions_roles = DB::table('role_permissions')
            ->select('role_permissions.*','permissions.name')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role_id', 1)
            ->get();

            $permissions = $permissions_roles->groupBy('permission_id');

        $role_permissions_data = DB::table('role_permissions')->where('company_id', $request->company_id)->where('role_id',$request->id)->get();
        $grouped_data = $role_permissions_data->groupBy('permission_id');

        $new_array = array();
        foreach($grouped_data as $accessKey => $permissionValue)
        {
            foreach($permissionValue as $valued)
            {
                $new_array["access"][$accessKey][] =  $valued->access_id;               
            }
            
        }

        $data['role'] = $role;
        $data['permissions'] = $permissions;
        $data['role_permissions'] = $new_array;

        return $this->sendResponse($data, 'Role Permissions retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $data = [];
        $role = DB::table('roles')->select('id','role')
            ->where('id',$request->role_id)
            ->where('company_id', $request->company_id)
            ->get();

        $role_permissions_data = DB::table('role_permissions')->where('company_id', $request->company_id)->where('role_id',$request->role_id)->get();
        $permissions = $role_permissions_data->groupBy('permission_id');

        $all_permissions = [];

        array_push($all_permissions, $role);

        foreach($permissions as $key => $permission){
            $value_array = [];
            $permission_array = array(
                'permission_id' => $key
            );

            array_push($value_array,$permission_array);

            foreach($permission as $access){
                $access_array = array(
                    'access_id' => $access->access_id
                );
                
                array_push($value_array,$access_array);
                
            }

            array_push($all_permissions, $value_array);
            
        }

        $result = json_encode($all_permissions);

        echo $result;
        
        exit;

        $data['role'] = $role;
        $data['permissions_access'] = $result;
        // $data['permissions'] = $permissions;

        return $this->sendResponse($data, 'Role Permissions retrieved successfully.');
    }


    public function role_permissions(Request $request)
    {
        //getting user permissions
        $user_permissions = DB::table('role_permissions')->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id');
        if($request->role_id!=1){
            $data =  $user_permissions->where('company_id', $request->company_id);
        }
        $data =  $user_permissions->where('role_id',$request->role_id)->get();
        if(!$data->isEmpty()){
            foreach($data as $val){
                $all_user_permissions[$val->name][] = $val->access_id;
            }
        //getting all roles & their permissions
            $role_permissions = DB::table('role_permissions')->select('name','access_id','permission_id')->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')->get();
            //print_r($role_permissions); exit;
            foreach($role_permissions as $permission){
                switch($permission->access_id){
                    case 1:
                        $action = 'create';
                        break;
                    case 2:
                        $action = 'read';
                        break;
                    case 3:
                        $action = 'update';
                        break;
                    case 4:
                        $action = 'delete';
                        break;
                    case 5:
                        $action = 'view invoice';
                        break;
                }
                if(isset($all_user_permissions[$permission->name]) && in_array($permission->access_id,$all_user_permissions[$permission->name])){
                    $access_id = 1;
                }
                else{
                    $access_id =0;
                }
                $all_permissions[$permission->permission_id][$action] = $access_id;
              
                //$all_permissions[$permission->name]
           
            }
            $only_permissions = DB::table('permissions')->get();
            foreach($only_permissions as $perm){
                $all_perm[$perm->name][] =   $all_permissions[$perm->id];
            }
            $calendar_bookings['success'] = true;
            $calendar_bookings['message'] = 'Permissions retrieved successfully';
    
            $calendar_bookings['data'] = $all_perm;
        }
        else{
            $calendar_bookings['success'] = false;
            $calendar_bookings['message'] = 'There is no data';
            $calendar_bookings['data'] = '';
        }
        
        
        //echo "<pre>"; print_r($all_permissions); exit;
       
        $result = json_encode($calendar_bookings);
        echo $result;
        exit;


    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'id' => 'required',
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $role = tap(DB::table('roles')->where('id', $request->id)->where('company_id', $request->company_id))
        ->update(['role' => $data['role']])
        ->first();

        if ($role == null) {
            return $this->sendResponse($role, 'Someting went wrong.');
        }
        $deletedRows = RolePermissions::where('role_id', $request->id)->where('company_id', $request->company_id)->delete();
        
        $data['access_id'] = json_decode($data['access_id'], TRUE);

        foreach($data['access_id'] as $key => $value){
            foreach($value as $val){
                $insert_data = array (
                    'role_id' => $request->id,
                    'permission_id' => $key,
                    'access_id' => $val,
                );
                $insert_data['company_id'] = $request->company_id;
                $data = RolePermissions::create($insert_data);
               
            }
        }
        return $this->sendResponse($role, 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'id' => 'required',
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $role = Role::find($request->id);

        if ($role == null) {
            return $this->sendResponse($role->toArray(), 'Someting went wrong.');
        }

        $deletedRows = RolePermissions::where('role_id', $request->id)->where('company_id', $request->company_id)->delete();

        $role->is_deleted = 1;
        if($role->save()){
            return $this->sendResponse($role->toArray(), 'Role deleted successfully.');
        }
    }
}
