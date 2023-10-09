<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([ 'status' => 'error', 'message' => 'Invalid Login' ], 503);
        }

        return response()->json([ 'status' => 'success', 'message' => 'Successful Login', 'token' => $user->createToken('user')->plainTextToken ], 200);
    }
}
