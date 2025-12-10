<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentImages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as FacadesStorage;

use function PHPSTORM_META\map;

class ApartmentImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $apartmentId)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10000'
        ]);

        $apartment = Apartment::findOrFail($apartmentId);

        $uploadedImages = [];

        foreach ($request->file('images') as $img) {
            $path = $img->store('apartments', 'public');
            $image = $apartment->images()->create(['image_path' => $path]);
            $uploadedImages[] = $image->image_url;
        }

        return response()->json([
            'message' => 'images stored successfully',
            'images' => $uploadedImages
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show($apartmentId)
    {
        $apartment = Apartment::findOrFail($apartmentId);

        return response()->json([
            'images' => $apartment->images->map->image_url
        ], 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10000'
        ]);
        $image = ApartmentImages::findOrFail($id);


        Storage::disk('public')->delete($image->image_path);


        $path = $request->file('image')->store('apartments', 'public');


        $image->update(['image_path' => $path]);

        return response()->json([
            'message' => 'Image updated successfully',
            'url' => $image->image_url
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $image = ApartmentImages::findOrFail($id);

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

}
