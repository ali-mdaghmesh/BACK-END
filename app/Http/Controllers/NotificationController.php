<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    
    function getNotifications(Request $request){

        $user=$request->user(); 
        $notifications=$user->notifications; 

        return response()->json(["notifications"=>$notifications]);
    }

    function deleteNotification(Request $request,$id){

        $user=$request->user(); 
        $notification=$user->notifications()->where('id',$id)->first(); 
        if($notification){
            $notification->delete(); 
            return response()->json(['message'=>'notification deleted successfully'],200);
        }
        return response()->json(["message"=>"notification not found"],404); 

    }




}
