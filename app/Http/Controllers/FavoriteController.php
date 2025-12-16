<?php
namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function getFavorites()
    {
        $user = Auth::user();

        $favorites = Favorite::where('user_id', $user->id)->get();

        if ($favorites->isEmpty()) {
            return response()->json(['message' => 'no favorites found'], 404);
        }

        return response()->json(['favorites' => $favorites], 200);
    }

    public function addToFavorites($apartment_id)
    {
        $user = Auth::user();

        $apartment = Apartment::find($apartment_id);

        if (!$apartment) {
            return response()->json(['message' => 'apartment not found'], 404);
        }

        $favoriteExists = Favorite::where('user_id', $user->id)
            ->where('apartment_id', $apartment_id)
            ->first();

        if ($favoriteExists) {
            return response()->json(['message' => 'apartment already in favorites'], 409);
        }

        $favorite = Favorite::create([
            'user_id' => $user->id,
            'apartment_id' => $apartment_id
        ]);

        return response()->json([
            'message' => 'apartment added to favorite successfully',
            'favorite' => $favorite
        ], 201);
    }

    public function removeFromFavorites($apartment_id)
    {
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('apartment_id', $apartment_id)
            ->first();

        if (!$favorite) {
            return response()->json(['message' => 'this apartment is not in your favorites'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'apartment removed from your favorites successfully'], 200);
    }
}
