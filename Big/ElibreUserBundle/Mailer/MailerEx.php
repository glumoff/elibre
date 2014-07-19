<?php

namespace Big\ElibreUserBundle\Mailer;

use FOS\UserBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserInterface;

class MailerEx extends Mailer implements MailerInterfaceEx {

  public function sendApprovementEmailMessage(UserInterface $user) {
    $template = 'BigElibreUserBundle:Registration:approvement.email.txt.twig';
    $url = $this->router->generate('big_elibre_admin', array('mode' => 'user'), true);
    $rendered = $this->templating->render($template, array(
        'user' => $user,
        'adminUrl' => $url
    ));
//    echo "<pre>";
//    var_dump($rendered);
//    exit;
    $this->sendEmailMessage($rendered, $this->parameters['from_email']['confirmation'], $this->parameters['from_email']['confirmation']);
  }

}
