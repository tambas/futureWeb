<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $type;
    protected $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $data)
    {
        $this->user = $user;
        $this->type = $data['type'];
        $this->token = $data['token'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.email-changed')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Azote.us - Changement d\'email')
                    ->with([
                        'user' => $this->user,
                        'type' => $this->type,
                        'token' => $this->token
                    ]);
    }
}
