<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
        } catch (ValidationException $e) {
            return response()->json([ 'status' => 'error', 'message' => 'Validation Error', 'messages' => $e->errors() ], 503);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([ 'status' => 'error', 'message' => 'Invalid Login' ], 503);
        }

        return response()->json([ 'status' => 'success', 'message' => 'Successful Login', 'token' => $user->createToken('user')->plainTextToken ], 200);
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ]);
        } catch (ValidationException $e) {
            return response()->json([ 'status' => 'error', 'message' => 'Validation Error', 'messages' => $e->errors() ], 503);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'user'
        ]);

        return response()->json([ 'status' => 'success', 'message' => 'Successful Registered' ], 200);
    }
}
