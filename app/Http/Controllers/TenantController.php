<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentRating;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\CancelReservationNotification;
use App\Notifications\EditReservationNotification;
use App\Notifications\NewReservationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class TenantController extends Controller
{


    function getTenantReservations(Request $request)
    {
        $user = Auth::user();
        $reservations = Reservation::where('tenant_id', $user->id)->with('apartment')->get();
        return response()->json(['reservations' => $reservations]);
    }


    function createReservation(Request $request,$id)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',

        ]);
        $apartment = Apartment::findOrFail($id);
        $owner=User::findOrFail($apartment->owner_id); 

        if(Reservation::where('tenant_id',Auth::user()->id)
                        ->where('apartment_id',$id)
                        ->where('end_date',$request->end_date)
                        ->where('start_date',$request->start_date)
                       ->exists()){
            return response()->json(['message'=>'you got reservation with same date '],409);
             }

        $reservation = $apartment->reservations()->create([
            'tenant_id' => $request->user()->id,
            'user_id' => $apartment->owner_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $this->getReservationPrice($request->start_date, $request->end_date, $apartment->price),
        ]);
        $owner->notify(new NewReservationNotification($apartment)); 

        return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservation], 201);
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


    function editReservation(Request $request, $reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);
           $owner=User::findOrFail($reservation->user_id); 
        if ($reservation->tenant_id != $request->user()->id) {
            return response()->json(['message' => 'you can only cancel your own reservations', 403]);
        }

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        $status = $reservation->status;

        if($status==='pending'){
            $reservation->start_date = $request->start_date;
            $reservation->end_date = $request->end_date;
            $reservation->save();
            return response()->json(['message' => 'Reservation updated successfully', 'reservation' => $reservation], 200);
        }else if($status==='approved'){
            $reservation->edit_start_date=$request->start_date;
            $reservation->edit_end_date=$request->end_date;
            $reservation->status='edit_requested';
            $reservation->save();
            $owner->notify(new EditReservationNotification());
            return response()->json(['message'=>'edit request sent successfully', 'reservation' => $reservation], 200);
        }
         return response()->json(['message'=>'this reservation cannot be edited'],403);

    }


    function cancelReservation(Request $request, $reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);
        $owner=User::findOrFail($reservation->user_id); 
        if ($reservation->tenant_id != $request->user()->id) {
            return response()->json(['message' => 'you can only cancel your own reservations'], 403);
        }
        $status=$reservation->status;
          if($status==='pending'){
              $reservation->status='cancelled';
               $reservation->save();
            return response()->json(['message'=>'reservation cancelled successfully','reservation'=>$reservation],200);
            }else if($status==='approved'){
               $reservation->status='cancel_requested';
               $reservation->save();
               $owner->notify(new CancelReservationNotification());
               return response()->json(['message'=>'cancel request sent successfully','reservation'=>$reservation],200);
        }else{
                return response()->json(['message'=>'this reservation cannot be cancelled',403]);
        }


    }


    public function rateApartment(Request $request, $apartmentId)
    {
        $user = Auth::user();
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5'
        ]);

        $apartment = Apartment::find($apartmentId);

        if(!$apartment) {
            return response()->json([
                'message' => 'Apartment not found'
            ], 404);
        }

        $rating = ApartmentRating::where('apartment_id', $apartment->id)
            ->where('tenant_id', $user->id)
            ->first();


         $reservations = Reservation::where('user_id', $user->id);

        $hasReservation = Reservation::where('tenant_id', $user->id)
            ->where('apartment_id', $apartment->id)
            ->where('status' , 'done')->exists();

        if (!$hasReservation) {
            return response()->json([
                'message' => 'You can only rate apartments you have reserved'
            ], 403);
        }


        if ($rating) {
           return response()->json('You have already rated this apartment',409);
        } else {
            $rating = ApartmentRating::create([
                'apartment_id' => $apartment->id,
                'tenant_id' => $user->id,
                'rating' => $request->rating
            ]);

            $apartment->ratings_sum += $request->rating;
            $apartment->ratings_count += 1;
        }


        $apartment->rating = $apartment->ratings_sum / $apartment->ratings_count;
        $apartment->save();

        return response()->json([
            'message' => 'You have rated this apartment',
            'rating' => $rating->rating,
        ] , 200);
    }


}



