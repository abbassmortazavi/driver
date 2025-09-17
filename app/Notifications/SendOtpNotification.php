<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  string  $otp
     * @param  string|null  $mobile
     * @param  string|null  $template
     * @param  int  $expiryMinutes
     */
    public function __construct(
        public string $otp,
        public ?string $mobile = null,
        public ?string $template = 'otplogin',
        protected int $expiryMinutes = 2
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'sms'];
    }

    /**
     * @param  object  $notifiable
     * @return string
     */
    public function toSms(object $notifiable): string
    {
        return "Your OTP code is {$this->otp}. Valid for {$this->expiryMinutes} minutes.";
    }

    /**
     * @param  object  $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'sendOtp',
            'mobile' => $this->mobile ?? $notifiable->mobile,
            'template' => $this->template,
            'otp' => $this->otp,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
            'mobile' => $this->mobile,
            'message' => "OTP sent to {$this->mobile}",
            'type' => 'otp_notification',
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}
