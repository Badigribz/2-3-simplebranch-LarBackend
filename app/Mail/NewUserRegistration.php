<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class NewUserRegistration extends Mailable
{
    public function __construct(public User $user)
    {
    }

    public function build()
    {
        return $this->subject('New Family Tree Registration')
                    ->view('emails.new-registration');
    }
}
