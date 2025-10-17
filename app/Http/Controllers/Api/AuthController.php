<?php



namespace App\Http\Controllers\Api;



use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Api\BaseController as BaseController;



use App\User;

use Validator;



class AuthController extends BaseController

{

    public function store(Request $request)

    {

        $validator = $this->validateEmail();

        if ($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors()); 

        }



        $user = User::where('email',$request->email)->where('is_active', 1)->first();

        if($user == null){

            return $this->sendError('','Credentials does not meet requirements please contact company owner!');

        }

        if(Hash::check($request->password,$user->password)){

            // $path = url('public/profiles', $user->image);

            // $user->image = $path;

            return $this->sendResponse($user->toArray(), 'User Login successfully.');

        }

        return $this->sendError('','Invalid Username or Passsword!');


    }



    public function validateEmail(){

        return Validator::make(request()->all(), [

            'email' => 'required|string|email|max:255',

        ]);

    }

}

