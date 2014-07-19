<?php

namespace Big\ElibreBundle\Controller;

/**
 * Description of DocumentController
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */

class RegisterController extends DefaultController {

  public function waitApprovementAction() {
        return $this->render('BigElibreBundle:Default:index.html.twig', $this->getTemplateParams());
  }

  protected function getTemplateParams() {
    if ($this->templateParams === NULL) {
      parent::getTemplateParams();
//      $this->get('translator')->setLocale('ru');
      $this->templateParams['content'] = $this->get('translator')->trans('register.approvement-wait', array(), 'messages');

    }
    return $this->templateParams;
  }
}
