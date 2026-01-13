<?php

namespace App\Models;

use App\Enums\Province;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $guarded = ['id'];
    use HasFactory;

    protected $casts = ['province' => Province::class,];

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function images(){
        return $this->hasMany(ApartmentImages::class);
    }
    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function apartmentRatings()
    {
        return $this->hasMany(ApartmentRating::class);
    }



}
