<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;


class NewReservationNotification extends Notification
{
    use Queueable;

    protected $apartment; 

    public function __construct($apartment)
    {
        $this->apartment=$apartment; 
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class,'database'];
    }


    public function toFcm(object $notifiable)
    {
        return (new FcmMessage(
            notification: FcmNotification::create()
            ->title('Reservation')
            ->body('a tenant has made a new reservation')
        )); 
    }


    public function toArray(object $notifiable): array
    {
        return [
            'title'=>'Reservation',
            'body'=>'your apartment on '. $this->apartment->country.' '.$this->apartment->province. ' has a new reservation request '
        ];
    }
}
