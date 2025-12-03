<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        
        $profile = Auth::user()->profile;

        return response()->json([
            'profile' => $profile,
            'profile_image_url' => asset('storage/' . $profile->profile_image),
            'identity_image_url' => asset('storage/' . $profile->identity_image),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $profile = Auth::user()->profile;
        $data = $request->validated();

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }
        if ($request->hasFile('identity_image')) {
            $data['identity_image'] = $request->file('identity_image')->store('identities', 'public');
        }

        $profile->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
            'profile_image_url' => asset('storage/' . $profile->profile_image),
            'identity_image_url' => asset('storage/' . $profile->identity_image),
        ], 200);
    }

    public function destroy(Request $request)
    {   $user=$request->user();
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['message' => 'Profile deleted successfully'], 200);
    }

}
