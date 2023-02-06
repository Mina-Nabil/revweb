<?php

namespace App\Notifications;

use App\Models\Users\Notification as UsersNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;


class RequestOfferCreated extends Notification implements ShouldQueue
{
    use Queueable;

    private $notification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UsersNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }


    public function toFcm($notifiable)
    {

        return FcmMessage::create()
            ->setData(json_decode($this->notification->data))
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle('New Offer Request')
                ->setBody($this->notification->body));

        //     ->setImage('http://example.com/url-to-image-here.png'))
        // ->setAndroid(
        //     AndroidConfig::create()
        //         ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
        //         ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
        // )->setApns(
        //     ApnsConfig::create()
        //         ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios')));
    }

    // /**
    //  * Get the mail representation of the notification.
    //  *
    //  * @param  mixed  $notifiable
    //  * @return \Illuminate\Notifications\Messages\MailMessage
    //  */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    // /**
    //  * Get the array representation of the notification.
    //  *
    //  * @param  mixed  $notifiable
    //  * @return array
    //  */
    public function toArray($notifiable)
    {
        return [
            'userType'  =>  $this->notification->notifiable_type,
            'userID'    =>  $this->notification->notifiable_id,
        ];
    }
}
