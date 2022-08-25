<?php

namespace App\Listeners;

use App\Events\ManagerAdded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SendEmailWithPassword
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ManagerAdded  $event
     * @return void
     */
    public function handle(ManagerAdded $event)
    {
        $user = User::find($event->user->id)->toArray();
        $password = $event->password;
        Mail::send('emails.new_manager_password', compact('user', 'password'), function($message) use ($user) {
            $message->to($user['email']);
            $message->subject('Email registered as Store manager.');
        });
        // Log::info("Email sent for new user " . $event->user->email . " ... Password: " . $event->password);
    }
}
