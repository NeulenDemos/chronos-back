<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('nazar.3taran.id@gmail.com', 'Chronos')
                    ->view('mails.reset_password')
                    ->text('mails.reset_password_plain')
                    ->with([
                        'login' => $this->obj->login,
                        'link' => $this->obj->link
                    ]);
    }
}
