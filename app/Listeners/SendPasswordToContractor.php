<?php

namespace App\Listeners;

use App\Events\ContractorRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Contractor;

class SendPasswordToContractor
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
     * @param  \App\Events\ContractorRegistered  $event
     * @return void
     */
    public function handle(ContractorRegistered $event)
    {
        $contractor = Contractor::find($event->contractor->id)->toArray();
        $password = $event->password;
        Mail::send('emails.new_contractor_password', compact('contractor', 'password'), function($message) use ($contractor) {
            $message->to($contractor['email']);
            $message->subject('Email registered as Contractor/Architecture.');
        });
        // Log::info("Email sent for new contractor " . $event->contractor->email . " ... Password: " . $event->password);
    }
}
