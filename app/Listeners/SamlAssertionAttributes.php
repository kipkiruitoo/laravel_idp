<?php

namespace App\Listeners;

// use App\Events\CodeGreenCreative\SamlIdp\Events\Assertion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use LightSaml\ClaimTypes;
use LightSaml\Model\Assertion\Attribute;
use CodeGreenCreative\SamlIdp\Events\Assertion;

class SamlAssertionAttributes
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
     * @param  Assertion  $event
     * @return void
     */
    public function handle(Assertion $event)
    {
        $event->attribute_statement
            ->addAttribute(new Attribute(ClaimTypes::EMAIL_ADDRESS, auth()->user()->email))
            ->addAttribute(new Attribute(ClaimTypes::SURNAME, auth()->user()->lname))
            ->addAttribute(new Attribute(ClaimTypes::GIVEN_NAME, auth()->user()->mname))
            ->addAttribute(new Attribute(ClaimTypes::COMMON_NAME, auth()->user()->fname))
            ->addAttribute(new Attribute(ClaimTypes::NAME, auth()->user()->name));
    }
}
