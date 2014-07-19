<?php

namespace Big\ElibreUserBundle\EventListener;

use Big\ElibreUserBundle\Mailer\MailerInterfaceEx;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegisterConfirmListener implements EventSubscriberInterface {

  private $mailer;
  private $router;

  public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router) {
    $this->mailer = $mailer;
    $this->router = $router;
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return array(
        FOSUserEvents::REGISTRATION_CONFIRM => 'onRegisterConfirm',
    );
  }

  public function onRegisterConfirm(GetResponseUserEvent $event) {
//        $url = $this->router->generate('homepage');
//        $event->setResponse(new RedirectResponse($url));
//    $event->stopPropagation();
    /** @var $user \FOS\UserBundle\Model\UserInterface */
    $user = $event->getUser();
    $user->setEnabled(false);

    if ($this->mailer instanceof MailerInterfaceEx) {
      $this->mailer->sendApprovementEmailMessage($user);
    }
    
    $url = $this->router->generate('register_wait_approvement');
    $event->setResponse(new RedirectResponse($url));
//    $event->setResponse(new Response("<pre>onRegisterConfirm</pre>"));
  }

}
