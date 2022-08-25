<?php

namespace App\Providers;

use App\Providers\ManagerAdded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
     * @param  \App\Providers\ManagerAdded  $event
     * @return void
     */
    public function handle(ManagerAdded $event)
    {
        //
    }
}
