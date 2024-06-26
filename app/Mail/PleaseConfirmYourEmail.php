<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PleaseConfirmYourEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The user associated with the email.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Create a new mailable instance.
     *
     * @param \App\Models\User $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the email.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.confirm-email');
    }
}
