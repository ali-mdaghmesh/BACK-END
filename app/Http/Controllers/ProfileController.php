<?php

namespace App\Http\Controllers;

use App\Enums\Province;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Apartment;
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

    public function filterApartments(Request $request)
    {
        if(!$request){
              $apartments = Apartment::get();
            return response()->json($apartments , 200);
        }
        $apartments = Apartment::query();

        $apartments->when($request->filled('province'), function ($q) use ($request) {

            $province = Province::from($request->input('province'));
            $q->where('province', $province->value); 
        });



        if ($request->has(['min_price', 'max_price']))

            $this->applyRangeFilter($apartments, $request, 'price', 'min_price', 'max_price');
        else
            $apartments->when($request->filled('price'), function ($q) use ($request) {
                $q->where('price', '=', $request->input('price'));
            });


        if($request->has(['min_rooms'  , 'max_rooms']))
        $this->applyRangeFilter($apartments, $request, 'rooms', 'min_rooms', 'max_rooms');
        else
            $apartments->when($request->filled('rooms'), function ($q) use ($request) {
                $q->where('rooms', '=', $request->input('rooms'));});

        return $apartments->get();
    }

    protected function applyRangeFilter($query, Request $request, $column, $minField, $maxField)
    {
        if ($request->filled($minField)) {
            $query->where($column, '>=', $request->input($minField));
        }

        if ($request->filled($maxField)) {
            $query->where($column, '<=', $request->input($maxField));
        }
    }


}
