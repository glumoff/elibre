<?php

namespace Big\ElibreBundle\Controller;

/**
 * Description of PageController
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
use Big\ElibreBundle\Entity\Page;

class PageController extends DefaultController {

  public function indexAction($page, $action) {
    $response = parent::indexAction($page, $action);
    //var_dump($response);
    if (!$response) {
      if ($action == 'save') {
        $this->SavePage();
        $attrs = $this->getRequest()->attributes;
        $page = $attrs->get('page');
        $_locale = $this->getRequest()->getLocale();
        $redirectUri = $this->generateUrl('big_elibre_page', array('_locale' => $_locale,
            'page' => $page, 'action' => 'view'
        ));
        //$response = new \Symfony\Component\HttpFoundation\Response($redirectUri);
        return $this->redirect($redirectUri);
      }
    }
    return $response;
  }

  protected function getTemplateParams() {
    if ($this->templateParams === NULL) {
      parent::getTemplateParams();
      $this->templateParams['page'] = $this->getRequest()->attributes->get('page');
    }
    return $this->templateParams;
  }

  protected function SavePage() {
    $request = $this->getRequest();
    $pageName = $request->attributes->get('page');
    $dbm = $this->getDoctrine()->getManager();
    /* @var $page Page */
    $pages = $dbm->getRepository("BigElibreBundle:Page")->findByName($pageName);
    if (!$pages) {
      throw $this->createNotFoundException('No page found for name "' . $pageName . '"');
    }

    $userLang = $request->getLocale();
    //$userLang = $request->getPreferredLanguage();
    $page = NULL;
    foreach ($pages as $curPage) {
      if ($curPage->getLang() == $userLang) {
        $page = $curPage;
        break;
      }
    }
    if (!isset($page)) {
      $page = $pages[0]; // перша ліпша
    }

    $page->setContent($request->request->get('content'));
    $dbm->flush();
  }

}
