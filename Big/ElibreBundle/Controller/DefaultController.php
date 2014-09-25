<?php

namespace Big\ElibreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Big\ElibreBundle\db\ElibreDBDelegate;

class DefaultController extends Controller {

  protected $templateParams = NULL;

  public function indexAction($page, $action) {
    $this->getTemplateParams();
    $this->templateParams['content'] = $this->getPageContent($page);
    $response = NULL;
    if ($action == 'edit') {
      $response = $this->render('BigElibreBundle:Default:editpage.html.twig', $this->getTemplateParams());
    } elseif ($action == 'view') { // view
      $response = $this->render('BigElibreBundle:Default:index.html.twig', $this->getTemplateParams());
    }
    return $response;
  }

  protected function getTemplateParams() {
    //var_dump($this->get('translator')->getLocale());
//    $this->get('translator')->setLocale($this->getRequest()->getPreferredLanguage());
    //var_dump($this->get('translator')->getLocale());

    if ($this->templateParams === NULL) {
      $this->templateParams = array();
      $this->templateParams['menuThemes'] = $this->getThemesMenuArray();
    }
    return $this->templateParams;
  }

  protected function getPageContent($pageName) {
//    $content = "getPageContent($page)";

    if (preg_match("/^[a-z]*$/", $pageName) == 0) {
      $pageName = "home";
    }

    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
                    'SELECT p
                      FROM BigElibreBundle:Page p
                      WHERE p.name = :name
                      ORDER BY p.id DESC'
            )->setParameter('name', $pageName);
    $pages = $query->getResult();

    if (is_array($pages) && (count($pages) > 0)) {
      //$content = var_export($pages, TRUE);
      $request = $this->getRequest();
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

      $content = $page->getContent();
    } else {
      $content = $this->get('translator')->trans('error.404', array(), 'messages');
    }

    return $content;
  }

  protected function getThemesMenuArray() {
    $db = new ElibreDBDelegate();
    $themesList = $db->getThemes(2);
    return $themesList->getThemesArray();
  }

}
