<?php

namespace App\Events;

use App\WebOrder;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class WebOrderWasPosted extends Event
{
    use SerializesModels;

    public $webOrder;

    /**
     * Create a new event instance.
     *
     * @param  WebOrder  $webOrder
     * @return void
     */
    public function __construct(WebOrder $webOrder)
    {
        $this->webOrder = $webOrder;
    }
}