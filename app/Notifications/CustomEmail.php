<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomEmail extends Notification
{
    use Queueable;

    protected $subject;
    protected $firstline;
    protected $title;
    protected $content;
    protected $actionUrl;
    protected $actionText;

    /**
     * Create a new notification instance.
     */
    public function __construct($subject, $title, $firstline, $content, $actionUrl, $actionText)
    {
        $this->subject = $subject;
        $this->firstline = $firstline;
        $this->title = $title;
        $this->content = $content;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
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
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.email', [
                'firstline' => $this->firstline,
                'title' => $this->title,
                'content' => $this->content,
                'actionUrl' => $this->actionUrl,
                'actionText' => $this->actionText,
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
            //
        ];
    }
}