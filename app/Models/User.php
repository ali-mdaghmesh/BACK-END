<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasFactory, HasApiTokens, Notifiable;


    protected $guarded = ['id'];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }


    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function ratings()
    {
    return $this->hasMany(ApartmentRating::class, 'tenant_id');
    }
    
    public function deviceTokens(){
        return $this->hasMany(DeviceToken::class);
    }

    public function routeNotificationForFcm($notification){
        return $this->deviceTokens->pluck('fcm_token')->toArray();
    }

}
