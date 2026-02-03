<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class UpcomingMaintenanceReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public Collection $websites;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $websites)
    {
        $this->websites = $websites;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->websites->count();
        $tomorrow = now()->addDay()->format('d.m.Y');

        return (new MailMessage)
            ->subject("⏰ Wartungs-Erinnerung: {$count} Website(s) morgen fällig ({$tomorrow})")
            ->view('emails.upcoming-maintenance-reminder', [
                'user' => $notifiable,
                'websites' => $this->websites,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'websites_count' => $this->websites->count(),
            'date' => now()->addDay()->format('Y-m-d'),
        ];
    }
}
