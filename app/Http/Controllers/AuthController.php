<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // login, return a token
    public function login(Request $request)
    {
        // validate request email and password
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // if validation fails
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }

        $credentials = $request->only(['email', 'password']);

        // check if authentication attempt fails
        if (!auth()->attempt($credentials)) {
            return $this->apiResponse(null, 'Invalid credentials', 401);
        }

        // create token
        $token = auth()->user()->createToken('authToken')->plainTextToken;

        return $this->apiResponse($this->authentificatedData($request, auth()->user(), $token), null, 200);
    }

    // register
    public function register(Request $request)
    {
        // validate request email and password
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required'
        ]);

        // if validation fails
        if ($validator->fails()) {
            return $this->apiResponse(null, $validator->errors(), 422);
        }

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // hash password
            'password' => bcrypt($request->password)
        ]);

        // generate jwt token
        $token = $user->createToken('authToken')->plainTextToken;

        return $this->apiResponse($this->authentificatedData($request, $user, $token), null, 201);
    }

    // logout
    public function logout(Request $request)
    {
        // revoke token
        $request->user()->currentAccessToken()->delete();

        return $this->apiResponse(null, null, 200);
    }

    // private authentificated date response
    public function authentificatedData(Request $request, User $user, $token)
    {
        $user = $user->load('roles');

        // add token to user
        $user->token = $token;

        return $user;
    }

}
