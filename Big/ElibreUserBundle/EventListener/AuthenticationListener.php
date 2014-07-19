<?php

namespace Big\ElibreUserBundle\EventListener;

use FOS\UserBundle\EventListener\AuthenticationListener as BaseAuthListener;
use FOS\UserBundle\FOSUserEvents;

class AuthenticationListener extends BaseAuthListener {

  public static function getSubscribedEvents() {
    return array(
        FOSUserEvents::REGISTRATION_COMPLETED => 'authenticate',
        //FOSUserEvents::REGISTRATION_CONFIRMED => 'authenticate', // unsubscribe from this event to do not do logon on registration confirmed
        FOSUserEvents::RESETTING_RESET_COMPLETED => 'authenticate',
    );
  }

}
