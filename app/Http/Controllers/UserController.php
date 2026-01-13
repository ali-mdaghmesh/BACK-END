<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function register(Request $request)
    {

        $request->validate([
            'phone_number' => 'required|string|max:15|unique:users,phone_number',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'profile_image_url' => 'required|image|mimes:webp,jpg,jpeg,png,gif|max:10000',
            'identity_image_url' => 'required|image|mimes:webp,jpg,jpeg,png,gif|max:10000',
            'role' => 'required|string|max:50',

        ]);


        $user = User::create([
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        $profileImagePath = $request->file('profile_image_url')->store('profiles', 'public');
        $identityImagePath = $request->file('identity_image_url')->store('identities', 'public');

        $profile = $user->profile()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'profile_image_url' => $profileImagePath,
            'identity_image_url' => $identityImagePath,
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
        ], 201);
    }

    function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('phone_number', 'password'))) {
            return response(['message' => 'Invalid login details'], 401);
        }

        $user = User::where('phone_number', $request->phone_number)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successfully',
            'role' => $user->role,
            'token' => $token
        ], 200);
    }

    function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successfully'], 200);
    }
}
