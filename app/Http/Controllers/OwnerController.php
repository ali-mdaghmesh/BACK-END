<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{

    function handlePendingReservation(Request $request, $reservation_id){

    $reservation = Reservation::findOrFail($reservation_id);

        $request->validate([
        'action' => 'required|in:approved,rejected',
    ]);

    if ($reservation->status !== 'pending') {
        return response()->json(['message' => 'Only pending reservations can be processed'],409);
    }

    if($this->hasConflict($reservation->apartment, $reservation->start_date,$reservation->end_date)&&$request->action==='approved'){
        return response()->json(['message'=>'the reservation dates conflict with an existing approved reservation'],409);
    }


    $reservation->status = $request->action;
    $reservation->save();

    return response()->json(['message' => "Reservation $request->action successfully",'reservation' => $reservation], 200);
}


      function hasConflict(Apartment $apartment, $startDate, $endDate){
        return $apartment->reservations()
        ->where('status', 'approved')
        ->where('start_date', '<=', $endDate)
        ->where('end_date', '>=', $startDate)
        ->exists();
}



    function handleCancelReservation(Request $request,$reservation_id){
        $reservation=Reservation::findOrFail($reservation_id); 
        $status=$reservation->status;
        $request->validate([
            'action'=>'required|in:accept,reject',
        ]);

        if($status==='cancel_requested'){
            if($request->action==='accept'){
                $reservation->status='cancelled';
                $reservation->save();
                return response()->json(['message'=>'reservation cancelled successfully','reservation'=>$reservation],200);
            }
            if($request->action==='reject'){
                $reservation->status='approved';
                $reservation->save();
                return response()->json(['message'=>'reservation cancelling rejected successfully','reservation'=>$reservation],200);
            }
    }
        return response()->json(['message'=>'invalid case'],400);
    
    }

    function handleEditReservation(Request $request,$reservation_id){
        $reservation=Reservation::findOrFail($reservation_id); 
        
        $status=$reservation->status;
        $request->validate([
            'action'=>'required|in:accept,reject',
        ]);
        if($status==='edit_requested'){
            if($request->action==='accept'){
                if($this->hasConflict($reservation->apartment, $reservation->edit_start_date,$reservation->edit_end_date)){
                    return response()->json(['message'=>'the edited reservation dates conflict with an existing approved reservation'],409);
                }
                $reservation->start_date=$reservation->edit_start_date;
                $reservation->end_date=$reservation->edit_end_date;
  
                $reservation->edit_start_date=null;
                $reservation->edit_end_date=null;
                $reservation->status='approved';
                $reservation->save();
                return response()->json(['message'=>'reservation edited successfully','reservation'=>$reservation],200);
            }else if($request->action==='reject'){
                $reservation->edit_start_date=null;
                $reservation->edit_end_date=null;
                $reservation->status='approved';
                $reservation->save();
                return response()->json(['message'=>'reservation edit rejected successfully','reservation'=>$reservation],200);
        }
    }
        return response()->json(['message'=>'invalid case'],400);
    }

    function getReservationPrice($startDate, $endDate, $pricePerNight)
    {
        if ($startDate == $endDate) {
            return $pricePerNight;
        }
        $start = date_create($startDate);
        $end = date_create($endDate);
        $nights = $start->diff($end)->days;

        return $nights * $pricePerNight;
    }

    function getApartmentReservations(Request $request,$apartment_id){
        $user=Auth::user();
        $apartment=Apartment::findOrFail($apartment_id);
        if($user->id!=$apartment->owner_id){
            return response()->json(['message'=>'you can only view your apartments reservations'],403);
        }
        $reservations=$apartment->reservations()->get();

        return response()->json(['reservations'=>$reservations],200);
    }

    function getReservationsByStatus(Request $request, $apartment_id){
    $request->validate([
        'status' => 'required|in:pending,approved,rejected,cancelled,edit_requested,cancel_requested'
    ]);

    $apartment = Apartment::findOrFail($apartment_id);

    $reservations = $apartment->reservations()->where('status', $request->status)->get();

    return response()->json([
        'reservations' => $reservations
    ], 200);
}

}
