<?php

namespace App\Mail;

use App\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Заявка, о которой нужно уведомить по электронной почте.
     * @var Ticket
     */
    protected $obTicket;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Ticket $obTicket)
    {
        $this->obTicket = $obTicket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $sFilePath = $this->obTicket["file_link"];

        $obMail = $this->from(config("tasks.mail_from"))
                       ->subject(__("task2.notification_subject"))
                       ->view("notification")
                       ->with([
                            "createdDate" => $this->obTicket["created_at"],
                            "name" => $this->obTicket["name"],
                            "email" => $this->obTicket["email"],
                            "phone" => $this->obTicket["phone"],
                            "hasFile" => ($sFilePath !== null)
                       ]);

        if ($sFilePath) {
            return $obMail->attachFromStorage($sFilePath);
        }

        return $obMail;
    }
}
