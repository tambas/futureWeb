<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\SupportRequest;

class TicketOpen extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $supportRequest;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, SupportRequest $supportRequest)
    {
        $this->user = $user;
        $this->supportRequest = $supportRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.open-ticket')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Azote.us - Ouverture ticket n°'.$this->supportRequest->id)
                    ->with([
                        'user' => $this->user,
                        'ticket' => $this->supportRequest
                    ]);
    }
}
