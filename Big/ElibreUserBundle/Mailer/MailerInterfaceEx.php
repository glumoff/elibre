<?php

namespace Big\ElibreUserBundle\Mailer;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;

interface MailerInterfaceEx extends MailerInterface {

  /**
   * Send an email to a admin to approve the account creation
   *
   * @param UserInterface $user
   *
   * @return void
   */
  public function sendApprovementEmailMessage(UserInterface $user);
}
