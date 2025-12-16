<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
                $apartments = Apartment::where('owner_id', $user->id)->with('images')->get();

        return response()->json($apartments);

    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return response()->json([
                'message' => 'Only owners can create apartments'
            ], 403);
        }

        $validated = $request->validate([
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'rooms' => 'required|integer|min:1|max:100',
            'price' => 'required|numeric|min:0',
        ]);


        $validated['owner_id'] = $user->id;
     $apartment = Apartment::create($validated);

        return response()->json([
            'message' => 'Apartment created successfully',
            'apartment' => $apartment,
        ], 201);
    }



    public function show($apartment_id)
    {
        $user = Auth::user();
        $apartment = Apartment::find($apartment_id);

        if (!$apartment) {
            return response()->json([
                'message' => 'Apartment not found'
            ], 404);
        }

        if ($user->role !== 'owner') {
            return response()->json([
                'message' => 'Only owners can view apartments'
            ], 403);
        }

        if (!$user->verified) {
            return response()->json([
                'message' => 'Your profile is not verified. You cannot view apartments.'
            ], 403);
        }

        if ($apartment->owner_id !== $user->id) {
            return response()->json([
                'message' => 'You can only view your own apartments'
            ], 403);
        }

        return response()->json([
            'apartment' => $apartment->load('images'),
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $apartment_id)
    {
        $user = Auth::user();

        $apartment = Apartment::find($apartment_id);

        if (!$apartment) {
            return response()->json([
                'message' => 'Apartment not found'
            ], 404);
        }



        if ($user->role !== 'owner') {
            return response()->json([
                'message' => 'Only owners can update apartments'
            ], 403);
        }

        if (!$user->verified) {
            return response()->json([
                'message' => 'Your profile is not verified. You cannot update apartments.'
            ], 403);
        }

        if ($apartment->owner_id !== $user->id) {
            return response()->json([
                'message' => 'You can only update your own apartments'
            ], 403);
        }



        $validated = $request->validate([
                'city' => 'sometimes|string|max:255',
                'province' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:255',
                'rooms' => 'sometimes|Integer|max:100',
                 'price' => 'sometimes|numeric|min:0'
        ]);

            $apartment->update($validated);

            return response(['message' =>'Apartment updated successfully' , 'apartment'=>$apartment],200);
        }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $apartment_id)
    {
        $user = Auth::user();

        $apartment = Apartment::find($apartment_id);

        if (!$apartment) {
            return response()->json([
                'message' => 'Apartment not found'
            ], 404);
        }

        if ($user->role !== 'owner') {
            return response()->json([
                'message' => 'Only owners can delete apartments'
            ], 403);
        }

        if (!$user->verified) {
            return response()->json([
                'message' => 'Your profile is not verified. You cannot delete apartments.'
            ], 403);
        }

        if ($apartment->owner_id !== $user->id) {
            return response()->json([
                'message' => 'You can only delete your own apartments'
            ], 403);
        }

        $apartment->delete();
        return response()->json([
            'message' => 'Apartment deleted successfully'
        ], 200);

    }





}
