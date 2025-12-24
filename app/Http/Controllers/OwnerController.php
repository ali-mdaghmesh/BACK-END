<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\AcceptReservationNotification;
use App\Notifications\HandleCancelReservationNotification;
use App\Notifications\HandleEditReservationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\json;

class OwnerController extends Controller
{

    function handlePendingReservation(Request $request, $reservation_id){

    $reservation = Reservation::findOrFail($reservation_id);
    $tenant=User::findOrFail($reservation->tenant_id); 
    $status=$reservation->status; 

        $request->validate([
        'action' => 'required|in:accept,reject',
    ]);

   if($status=='pending'){
    if($request->action==='accept'){
    if($this->hasConflict($reservation->apartment, $reservation->start_date,$reservation->end_date)){
        return response()->json(['message'=>'the reservation dates conflict with an existing approved reservation'],409);
    }
    $reservation->status ='approved';
    $reservation->save();
    $tenant->notify(new AcceptReservationNotification());
    return response()->json(['message'=>'the reservation accepted successfully'],200); 
   }
    }else if($status=='reject'){
        $reservation->status='rejected'; 
        $reservation->save(); 
        return response()->json(['message'=>'the reservation rejected successfully'],200);
   }
   return response()->json(['message'=>'this reservation cannot be handled'],400); 
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
        $tenant=User::findOrFail($reservation->tenant_id);
        $request->validate([
            'action'=>'required|in:accept,reject',
        ]);

        if($status==='cancel_requested'){
            if($request->action==='accept'){
                $reservation->status='cancelled';
                $reservation->save();
                $tenant->notify(new HandleCancelReservationNotification('accepted'));
                return response()->json(['message'=>'reservation cancelled successfully','reservation'=>$reservation],200);
            }
            if($request->action==='reject'){
                $reservation->status='approved';
                $reservation->save();
                $tenant->notify(new HandleCancelReservationNotification('rejected')); 
                return response()->json(['message'=>'reservation cancelling rejected successfully','reservation'=>$reservation],200);
            }
    }
        return response()->json(['message'=>'this reservation cannot be canceled'],400);
    
    }

    function handleEditReservation(Request $request,$reservation_id){
        $reservation=Reservation::findOrFail($reservation_id); 
        $status=$reservation->status;
        $request->validate([
            'action'=>'required|in:accept,reject',
        ]);
        $tenant=User::findOrFail($reservation->tenant_id);
        if($status==='edit_requested'){
            if($request->action==='accept'){
                if($this->hasConflict($reservation->apartment, $reservation->edit_start_date,$reservation->edit_end_date)){
                    return response()->json(['message'=>'the edited reservation dates conflict with an existing approved reservation'],409);
                }
                $reservation->start_date=$reservation->edit_start_date;
                $reservation->end_date=$reservation->edit_end_date;
                $reservation->total_price=$this->getReservationPrice($reservation->start_date,$reservation->end_date,$reservation->apartment->price);
                $reservation->edit_start_date=null;
                $reservation->edit_end_date=null;
                $reservation->status='approved';
                $reservation->save();
                $tenant->notify(new HandleEditReservationNotification('accepted'));
                return response()->json(['message'=>'reservation edited successfully','reservation'=>$reservation],200);
            }else if($request->action==='reject'){
                $reservation->edit_start_date=null;
                $reservation->edit_end_date=null;
                $reservation->status='approved';
                $reservation->save();
                $tenant->notify(new HandleEditReservationNotification('rejected')); 
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

        return $nights*$pricePerNight;
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

    return response()->json(['reservations' => $reservations], 200);
}

}
