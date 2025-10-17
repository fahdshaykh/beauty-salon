<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\User;
use DB;
use Hash;
use Validator;
use Str;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where('role_id', '!=', 1)->where('company_id', $request->company_id)->get();
        if(count($users)>0){
        $i = 0;
        foreach($users as $key => $user){
            $path = url('public/profiles',$user->image);
            $users[$i]['image'] = $path;

            $roles = DB::table('roles')->where('is_deleted', 0)->where('id', $user->role_id)->get();

            foreach($roles as $role){
                $users[$key]['role_name'] = $role->role;
            }
            
            $i++;
        }

        if ($users->isEmpty()) {
            return $this->sendResponse($users->toArray(), 'Something went wrong.');
        }

        return $this->sendResponse($users->toArray(), 'Users retrieved successfully.');
    }
    else{
        return $this->sendResponse($users->toArray(), 'No users found.');
    }
    }

    public function userImage(Request $request)
    {
        $users = User::select('image')->where('company_id', $request->company_id)->get();

        return response()->download(public_path('profiles/'.$users['0']->image), 'user image');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'company_id' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->role_id = $request->input('role_id');

        if($request->image){
            $originalImage = $request->file('image');
            $originalImage->move(public_path().'/profiles', $user_image = 'img_'.Str::random(15).'.jpg');
            $user->image = $user_image;
        }
        
        if($request->input('password')!=""){
            $user->password = Hash::make($request->input('password'));
        }

        $user->company_id = $request->input('company_id');
        if($user->save()){
            return $this->sendResponse($user->toArray(), 'User created successfully.');
        }
    }

    public function user_status(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'id' => 'required',
            'status' => 'required',
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $book = tap(DB::table('users')
        ->where('id', $request->id)->where('company_id', $request->company_id))
        ->update(['is_active' => $request->status])
        ->first();

        if ($book == null) {
            return $this->sendResponse((array)$book, 'Something went wrong.');
        }

        return $this->sendResponse($book, 'User Status changed successfully.');

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

        $user = DB::table('users')
                            ->select('id','role_id','company_id','name','email','image')
                            ->where('company_id', $request->company_id)
                            ->where('id',$request->id)
                            ->get();
 
        if ($user->isEmpty()) {
            return $this->sendResponse($user->toArray(), 'Something went wrong.');
        }

        return $this->sendResponse($user->toArray(), 'User retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $input = $request->all();

        $validator = Validator::make($input, [
            'id' => 'required',
            'company_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'role_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $user = User::find($request->id);
        if ($user == null) {
            return $this->sendError('user not found.');
        }
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->role_id = $request->input('role_id');

        if($request->image){
            $originalImage = $request->file('image');
            $originalImage->move(public_path().'/profiles', $user_image = 'img_'.Str::random(15).'.jpg');
            $user->image = $user_image;
        }
        
        if($request->input('password')!=""){
            $user->password = Hash::make($request->input('password'));
        }

        $user->company_id = $request->company_id;
        if($user->save()){
            return $this->sendResponse($user->toArray(), 'User updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Request $request)
    // {
    //     $input = $request->all();

    //     $validator = Validator::make($input, [
    //         'id' => 'required',
    //         'company_id' => 'required'
    //     ]);

    //     if($validator->fails()){
    //         return $this->sendError('Validation Error.', $validator->errors());       
    //     }

    //     $user = tap(DB::table('users')
    //     ->select('name','email')
    //     ->where('id', $request->id)->where('company_id', $request->company_id))
    //     ->update(['is_deleted' => 1])
    //     ->first();
        
    //     if ($user == null) {
    //         return $this->sendResponse($user, 'Something went wrong.');
    //     }

    //     return $this->sendResponse($user, 'User deleted successfully.');
        
    // }

    public function device_token(Request $request)

    {
        //$user = '';
        //return $this->sendResponse($user, 'User Token has updated');
        //dd($request->userid);

        $user = tap(DB::table('users')
        ->where('id', $request->userid))
        ->update(['device_token' => $request->token])
        ->first();

        //$path = url('public/profiles', $user->image);
        //$user->image = $path;

        return $this->sendResponse($user, 'User Token has updated');

    }

    public function profile($id)

    {

        $user = User::find($id);

        return view('users.profile',compact('user'));

    }



    public function profile_update(Request $request, $id)

    {

        $validate = $request->validate([

            'name' => 'required',

            'email' => 'required|max:255',

        ]);

        $user_image = '';

        $user_data = User::select('*')->where('company_id', auth()->user()->company_id)->where('id',$id)->get();



        foreach($user_data as $get_data){

            $user_image = $get_data->image;

        }



        if($request->image){

            $validate = $request->validate([

                'image' => 'mimes:jpeg,jpg,png,gif|required|max:2048',

            ]);

            $originalImage = $request->file('image');

            $originalImage->move(public_path().'/profiles', $user_image = 'img_'.Str::random(15).'.jpg');

        }



        $user = User::find($id);

        $user->name = $request->input('name');

        $user->email = $request->input('email');

        $user->image = $user_image;

        if($request->input('password')!=""){

            $user->password = Hash::make($request->input('password'));

        }

        $user->company_id = auth()->user()->company_id;

        $user->save();

        session()->flash('success','Your Profile has updated!');

        return redirect('/home');

    }
    
}
