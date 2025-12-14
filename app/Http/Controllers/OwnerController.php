<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    //

    function handlePendingReservation(Request $request,$reservation_id){
        $user=Auth::user(); 
        $reservation=Reservation::findOrFail($reservation_id); 
        if($reservation->status!='pending'){
            return response()->json(['message'=>'this reservation is not pending'],400);
        }
        if($user->id!=$reservation->user_id){
            return response()->json(['message'=>'you can only manage your reservations'],403);
        }
        $request->validate([
            'action'=>'required|in:approve,reject'
        ]);
        if($request->action=='approve'){
            if( $this->hasConflict($reservation->apartment,$reservation->start_date,$reservation->end_date)){
                return response()->json(['message'=>'the reservation dates conflict with an existing reservation'],409);
            }
            $reservation->status='approved';
            $reservation->save();
            return response()->json(['message'=>'reservation approved successfully','reservation'=>$reservation],200);
        }
            $reservation->status='rejecte';
            $reservation->save();
            return response()->json(['message'=>'reservation rejected successfully','reservatoin'=>$reservation],200);
    }

      function hasConflict(Apartment $apartment,$startDate,$endDate){
        return $apartment->reservations()
        ->where('start_date','<=',$endDate)
        ->where('end_date', '>=',$startDate)
        ->where('status','approved')
        ->exists();
      }

    function handleCancelReservation(Request $request,$reservation_id){
        $user=Auth::user(); 
        $reservation=Reservation::findOrFail($reservation_id); 
        $status=$reservation->status;
        if($user->id!=$reservation->user_id){
            return response()->json(['message'=>'you can only manage your reservations'],403);
        }
        if($status==='cancel_requested'||$status==='edit_requested'){
            return response()->json(['message'=>'your previous request is still pending'],400);
        }
        if($status=='cancelled'){
            return response()->json(['message'=>'this reservation is already cancelled'],400);
        }
        if($status=='rejected'){
            return response()->json(['message'=>'this reservation is already rejected'],400);
        }
        $request->validate([
            'action'=>'required|in:cancel'
        ]);

        if($request->action==='cancel'){
        if($status==='pending'){
            $reservation->status='cancelled';
            $reservation->save();
            return response()->json(['message'=>'reservation cancelled successfully','reservation'=>$reservation],200);
        }
        if($status=='approved'){
            $reservation->status='cancel_requested';
            $reservation->save();
            return response()->json(['message'=>'reservation cancel requested successfully','reservation'=>$reservation],200);
        }
    
    }
        return response()->json(['message'=>'invalid case'],400);
    

        
    }

    function handleEditeReservation(Request $request,$reservation_id){
        $user=Auth::user(); 
        $reservation=Reservation::findOrFail($reservation_id); 
        $status=$reservation->status;
         $request->validate([
            'new_start_date'=>'required|date|after_or_equal:today',
            'new_end_date'=>'required|date|after_or_equal:new_start_date',
        ]);
        if($status=='edit_requested' || $status=='cancel_requested'){
            return response()->json(['message'=>'your previous request is still pending '],400);
        }
        if($status=='cancelled'){
            return response()->json(['message'=>'this reservation is cancelled'],400);
        }
        if($status=='rejected'){
            return response()->json(['message'=>'this reservation is rejected'],400);
        }
        if($user->id!=$reservation->user_id){
            return response()->json(['message'=>'you can only manage your reservations'],403);
        }
       
        
        if($status==='pending'){
            $reservation->edit_start_date=$request->new_start_date;
            $reservation->edit_end_date=$request->new_end_date;
            $reservation->save();
            return response()->json(['message'=>'reservation edited successfully','reservation'=>$reservation],200);
        }
        if($status==='approved'){
            $reservation->status='edit_requested';
            $reservation->edit_start_date=$request->new_start_date;
            $reservation->edit_end_date=$request->new_end_date;
            $reservation->save();
            return response()->json(['message'=>'reservation edit requested successfully','reservation'=>$reservation],200);
        }
        return response()->json(['message'=>'invalid case'],400);
    }

    function getReservations(Request $request,$apartment_id){
        $user=Auth::user();
        $apartment=Apartment::findOrFail($apartment_id);
        if($user->id!=$apartment->owner_id){
            return response()->json(['message'=>'you can only view your apartments reservations'],403);
        }
        $reservations=$apartment->reservations()->get();

        return response()->json(['reservations'=>$reservations],200);
    }

    function getApprovedReservations(Request $request,$apartment_id){
        $user=Auth::user();
        $apartment=Apartment::findOrFail($apartment_id);
        if($user->id!=$apartment->owner_id){
            return response()->json(['message'=>'you can only view your apartments reservations'],403);
        }
        $reservations=$apartment->reservations()->where('status','approved')->get();

        return response()->json(['reservations'=>$reservations],200);
    }

    function getCancelledReservations(Request $request,$apartment_id){
        $user=Auth::user();
        $apartment=Apartment::findOrFail($apartment_id);
        if($user->id!=$apartment->owner_id){
            return response()->json(['message'=>'you can only view your apartments reservations'],403);
        }
        $reservations=$apartment->reservations()->where('status','cancelled')->get();

        return response()->json(['reservations'=>$reservations],200);
    }

    function getPendingReservations(Request $request,$apartment_id){
        $user=Auth::user();
        $apartment=Apartment::findOrFail($apartment_id);
        if($user->id!=$apartment->owner_id){
            return response()->json(['message'=>'you can only view your apartments reservations'],403);
        }
        $reservations=$apartment->reservations()->where('status','pending')->get();

        return response()->json(['reservations'=>$reservations],200);
    }

    function getRejectedReservations(Request $request,$apartment_id){
        $user=Auth::user();
        $apartment=Apartment::findOrFail($apartment_id);
        if($user->id!=$apartment->owner_id){
            return response()->json(['message'=>'you can only view your apartments reservations'],403);
        }
        $reservations=$apartment->reservations()->where('status','rejected')->get();

        return response()->json(['reservations'=>$reservations],200);
    }

    function getEditReservations(Request $request,$apartment_id){
        $user=Auth::user();
        $apartment=Apartment::findOrFail($apartment_id);
        if($user->id!=$apartment->owner_id){
            return response()->json(['message'=>'you can only view your apartments reservations'],403);
        }
        $reservations=$apartment->reservations()->where('status','edit_requested')->get();

        return response()->json(['reservations'=>$reservations],200);
    }

    function getCancelRequestedReservations(Request $request,$apartment_id){
        $user=Auth::user();
        $apartment=Apartment::findOrFail($apartment_id);
        if($user->id!=$apartment->owner_id){
            return response()->json(['message'=>'you can only view your apartments reservations'],403);
        }
        $reservations=$apartment->reservations()->where('status','cancel_requested')->get();

        return response()->json(['reservations'=>$reservations],200);
    }
}
