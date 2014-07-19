<?php

namespace Big\ElibreUserBundle\EventListener;

use FOS\UserBundle\EventListener\EmailConfirmationListener as BaseConfirmListener;
//use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
//use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

//use Symfony\Component\HttpFoundation\Response;

class EmailConfirmationListener extends BaseConfirmListener {

  private $mailer;
  private $tokenGenerator;
  private $router;
  private $session;

  public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $router, SessionInterface $session) {
    $this->mailer = $mailer;
    $this->tokenGenerator = $tokenGenerator;
    $this->router = $router;
    $this->session = $session;
  }

  public function onRegistrationSuccess(FormEvent $event) {
    /** @var $user \FOS\UserBundle\Model\UserInterface */
    $user = $event->getForm()->getData();

    $user->setEnabled(false);
    if (null === $user->getConfirmationToken()) {
      $user->setConfirmationToken($this->tokenGenerator->generateToken());
    }

    $this->mailer->sendConfirmationEmailMessage($user);

    $this->session->set('fos_user_send_confirmation_email/email', $user->getEmail());

    $url = $this->router->generate('fos_user_registration_check_email');
    $event->setResponse(new RedirectResponse($url));
//    $event->setResponse(new \Symfony\Component\HttpFoundation\Response($url));
  }

}
