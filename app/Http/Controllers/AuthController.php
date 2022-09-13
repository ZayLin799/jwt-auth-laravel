<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Http\Controllers\BaseController as BaseController;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:2|max:100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|confirmed|min:6',
            ]);

            if($validator->fails()) {
                return $this->handleError($validator->errors());
            }

            $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password)
                    ]);

            $token = auth()->attempt(['name'=>$request->name,'password'=>$request->password]);
            $success['token'] = $this->respondWithToken($token);
            $success['user'] = $user;

            return $this->handleResponse($success, 'User successfully registered');

        }  catch (\Exception $e) {

            return $this->handleError($e->getMessage());

        }
    }

    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if($validator->fails()) {
                return $this->handleError($validator->errors());
            }

            if (!$token = auth()->attempt($validator->validated())) {
                return $this->handleError('Unauthorised.', ['error' => 'Unauthorised'],401);
            } else {
                $user = Auth::user();
                $profile = User::where('id', $user->id)->first();
                $success['token'] = $this->respondWithToken($token);
                $success['user'] = $user;

                return $this->handleResponse($success, 'User logged-in!');
            }

        }  catch (\Exception $e) {

            return $this->handleError($e->getMessage());

        }
    }


    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json(['status' => 'success', 'message' => 'User logged out successfully']);

    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

}
