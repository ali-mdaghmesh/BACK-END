<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $guarded = ['id'];
    use HasFactory;

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function images(){
        return $this->hasMany(ApartmentImages::class);
    }
}
