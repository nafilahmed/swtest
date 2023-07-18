<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use Validator;
use Hash;

class AuthController extends Controller
{
    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:55',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors()]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $accessToken = $user->createToken('example')->accessToken;

        return Response([ 'user' => $user, 'access_token' => $accessToken],200);
    }
    /**
     * Display a listing of the resource.
     */
    public function loginUser(Request $request): Response
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors()]);
        }

        if (!auth()->attempt($data)) {
            return response(['message' => 'Login credentials are invaild']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return Response(['status' => 200,'token' => $accessToken],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getUserDetail(): Response
    {
        $user = User::all();
        return Response(['data' => $user],200);
    }

    /**
     * Display the specified resource.
     */
    public function userLogout(): Response
    {
        $accessToken = Auth::guard('api')->user()->token();

        \DB::table('oauth_refresh_tokens')
        ->where('access_token_id', $accessToken->id)
        ->update(['revoked' => true]);
        $accessToken->revoke();

        return Response(['data' => 'Unauthorized','message' => 'User logout successfully.'],200);
    }    
}
