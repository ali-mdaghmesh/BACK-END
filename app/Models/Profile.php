<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'profile_image_url',
        'identity_image_url',
        'date_of_birth',
    ];
}
