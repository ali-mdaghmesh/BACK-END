<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class HandleCancelReservationNotification extends Notification
{
    use Queueable;

    protected $action;
    public function __construct($action)
    {
        $this->action=$action;
    }

    public function via(object $notifiable): array
    {
        return [FcmChannel::class,'database'];
    }

 
    public function toFcm($notifiable)
    {
    return (new FcmMessage(
        notification: FcmNotification::create()
            ->title('Reservation')
            ->body("your cacel request has been $this->action")
    ));
}


    public function toArray(object $notifiable): array
    {
        return [
            'title'=>'Reservation',
            'body'=>'your cancel request has been '.$this->action
        ];
    }
}
