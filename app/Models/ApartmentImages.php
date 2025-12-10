<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApartmentImages extends Model
{
    protected $guarded = ['id'];

    use HasFactory;

    public function apartment(){
        return $this->belongsTo(Apartment::class , 'apartment_id');
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);

    }


}
