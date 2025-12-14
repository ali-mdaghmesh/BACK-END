<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\get;
use function Pest\Laravel\json;

class TenantController extends Controller
{
    

    function getTenantReservations(Request $request){
            $user=Auth::user(); 
            $reservations=Reservation::where('tenant_id',$user->id)->with('apartment')->get();
            return response()->json(['massege'=>'succeded','reservations'=>$reservations]); 
    }


    function createReservation(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
           'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            
        ]);

        $apartment = Apartment::findOrFail($request->apartment_id);
        $reservation=$apartment->reservations()->create([
            'tenant_id' => $request->user()->id,
            'user_id' => $apartment->owner_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $this->getReservationPrice($request->start_date, $request->end_date, $apartment->price),
        ]);
 
        return response()->json(['message' => 'Reservation created successfully','reservation'=>$reservation], 201);
    }

    function hasConflict(Apartment $apartment,$startDate,$endDate){
        return $apartment->reservations()
        ->where('start_date','<=',$endDate)
        ->where('end_date', '>=',$startDate)
        ->exists();
    }
   
   function getReservationPrice($startDate, $endDate, $pricePerNight){
    if($startDate==$endDate){
        return $pricePerNight;
    }
    $start = date_create($startDate);
    $end   = date_create($endDate);
    $nights = $start->diff($end)->days;

    return $nights * $pricePerNight;
}


    function editReservation(Request $request,$reservation_id){
        $reservation=Reservation::findOrFail($reservation_id);
        if($reservation->tenant_id != $request->user()->id){
            return response()->json(['message' => 'you can only cancel your own reservations'], 403);
        }
        
        $request->validate([
            'edit_start_date'=>'nullable|date|after_or_equal:today',
            'edit_end_date'=>'nullable|date|after_or_equal:edit_start_date',
        ]);
        if($reservation->status==='pending'){
            $reservation->start_date=$request->edit_start_date;
            $reservation->end_date=$request->edit_end_date;
            $reservation->total_price=$this->getReservationPrice($request->start_date, $request->end_date, $reservation->apartment->price);
            $reservation->status='pending';
            $reservation->save();
            return response()->json(['message' => 'Reservation updated successfully','reservation'=>$reservation], 200);
        }
        if($reservation->status=='edit_requested'){
            return response()->json(['message' => 'your previous edit request is still pending'], 403);
        }
        if($reservation->status=='cancel_requested'){
            return response()->json(['message' => 'you can not edit a reservation with a pending cancel request'], 403);
        }
        if($reservation->status=='cancelled'){
            return response()->json(['message' => 'you can not edit a cancelled reservation'], 403);
        }
        if($reservation->status=='rejected'){
            return response()->json(['message' => 'you can not edit a rejected reservation'], 403);
        }
        if($reservation->status=='approved'){
            $reservation->edit_start_date=$request->start_date;
            $reservation->edit_end_date=$request->end_date;
            $reservation->status='edit_requested';
            $reservation->save();
            return response()->json(['message' => 'Edit request sent successfully','reservation'=>$reservation], 200);
        }
            
    }


    function cancelReservation(Request $request,$reservation_id){
        $reservation=Reservation::findOrfail($reservation_id);
        if($reservation->tenant_id != $request->user()->id){
            return response()->json(['message' => 'you can only cancel your own reservations'], 403);
        }
        if($reservation->status=='approved'){
            $reservation->status='cancel_requested';
            $reservation->save();
            return response()->json(['message' => 'Cancel request sent successfully','reservation'=>$reservation], 200);
        }
        if($reservation->status=='cancelled'){
            return response()->json(['message' => 'the reservation is already cancelled'], 403);
        }
        if($reservation->status=='pending'){
            $reservation->status='cancelled';
            $reservation->save();
            return response()->json(['message' => 'Reservation cancelled successfully','reservation'=>$reservation], 200);
        }
        if($reservation->status=='rejected'){
            return response()->json(['message' => 'you can not cancel a rejected reservation'], 403);
        }
        if($reservation->status=='cancel_requested'){
            return response()->json(['message' => 'your previous cancel request is still pending'], 403);
        }
        
        if($reservation->status=='edit_requested'){
            return response()->json(['message' => 'you can not cancel a reservation with a pending edit request'], 403);
        }
     
    }
      

    }
    

