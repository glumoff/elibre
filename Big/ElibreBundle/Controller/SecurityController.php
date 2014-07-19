<?php

/**
 * Description of SecurityController
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */

namespace Big\ElibreBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Intl\Locale;

class SecurityController extends DefaultController {

  public function loginAction() {
    return $this->render('BigElibreBundle:Default:login.html.twig', $this->getTemplateParams());
  }

  public function registerAction() {
    return $this->render('BigElibreBundle:Default:register.html.twig', $this->getTemplateParams());
  }

  protected function getTemplateParams() {
    parent::getTemplateParams();

    $request = $this->getRequest();
    $session = $request->getSession();
    if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
      $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
    } else {
      $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
      $session->remove(SecurityContext::AUTHENTICATION_ERROR);
    }

    $error_msg = '';
//    echo get_class($error);
    if (is_a($error, 'Exception')) {
      $error_msg = $error->getMessageKey();
    }

    $this->templateParams['last_username'] = $session->get(SecurityContext::LAST_USERNAME);
//    $this->templateParams['csrf_token'] = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
    $this->templateParams['error_msg'] = $this->get('translator')->trans($error_msg, array(), 'security');

    return $this->templateParams;
  }

}
