<?php

namespace App\Notifications;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;


class RequestOfferCreated extends Notification
{
    use Queueable;

    private $userType;
    private $userID;
    private $carID;
    private $carModel;
    private $carBrand;
    private $carCategory;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userType, $userID, string $carBrand, string $carModel, string $carCategory, int $carID)
    {
        $this->carBrand = $carBrand;
        $this->carModel = $carModel;
        $this->carCategory = $carCategory;
        $this->userType = $userType;
        $this->userID = $userID;
        $this->carID = $carID;
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

        Log::debug("Barmy FCM message");
        return FcmMessage::create()
            ->setData(['model' => $this->carModel, 'brand' => $this->carBrand])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle('New Offer Request')
                ->setBody("New offer requested created for {$this->carBrand} {$this->carModel} - {$this->carCategory} "));

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
            'userType'  =>  $this->userType,
            'userID'    =>  $this->userID,
            'car'  =>   $this->carID,
        ];
    }
}
