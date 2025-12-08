<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    

    function findUsers(Request $request){
        
        $query = $request->input('query');

  $users=User::with('profile')->where('phone_number','like','%'.$query.'%')
                            ->orWhere('first_name','like','%'.$query.'%')
                            ->orWhere('last_name','like','%'.$query.'%')
                           -> orWhere('id','like','%'.$query.'%')->get();
    
    if(!$users){
        return response()->json(['message'=>'no sush users found'],404);  
    }
    return response()->json(['message'=>'success','users'=>$users],200);
    }

    function changeRole(Request $request,$id){
        $user=User::with('profile')->find($id);
        if(!$user){
            return response()->json(['message'=>'user not found'],404);
        }
        $user->profile->verified=true;
        $user->profile->role=$request->role;
        $user->profile->save();
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
        $user->profile->verified=true;
        $user->profile->save();
        return response()->json(['message'=>'user verified successfuly','user'=>$user],200);
    }

    function getUnverifiedUsers(Request $request){
        $profiles=Profile::where('verified',false)->get();
        return response()->json(['message'=>'success','users'=>$profiles],200);
    }







}
