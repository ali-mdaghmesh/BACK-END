<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;

class AdminController extends Controller
{


    function findUsers(Request $request){
        $phone = $request->input('phone_number');


        $users = User::with('profile')
            ->when($phone, fn($q) => $q->where('phone_number', 'like', "%$phone%"))
         ->get();


        if ($users->isEmpty()) {
        return response()->json(['message'=>'no such user found'],404);
    }
    return response()->json(['message'=>'success','users'=>$users],200);
    }

    function changeRole(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'user not found'], 404);
        }
        if($user ->role==$request->role){
            return response()->json(['message'=>'user already has this role'],409);
        }
        $user->verified = true;
        $user->role = $request->role;
        $user->save();
        return $user;
    }

    function deleteUser(Request $request,$id){
        $user=User::find($id);
        if(!$user){
            return response()->json(['message'=>'user not found'],404);
        }
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['message'=>'user deleted successfully'],200);
    }

    function verifyUser(Request $request,$id){
        $user=User::find($id);
        if(!$user){
            return response()->json(['message'=>'user not found'],404);
        }
        if($user->verified){
            return response()->json(['message'=>'user already verified'],409);
        }
        $user->verified=true;
        $user->save();
        return response()->json(['message'=>'user verified successfuly','user'=>$user],200);
    }

    public function getUnverifiedUsers()
    {
        $users = User::where('verified', false)->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No unverified users found'], 404);
        }

        return response()->json([
            'message' => 'success',
            'users' => $users
        ], 200);
    }










}
