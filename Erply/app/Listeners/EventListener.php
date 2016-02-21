<?php

namespace App\Listeners;

use App\Events\WebOrderWasPosted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubmitToErply
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
     * @param  WebOrderWasPosted  $event
     * @return void
     */
    public function handle(WebOrderWasPosted $event)
    {
        //
        var_dump($event);
    }
}
