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
    $this->templateParams['DIRECTORY_SEPARATOR'] = DIRECTORY_SEPARATOR;
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
    $themesList = $db->getThemes(NULL, $this->getUserRole());
    return $themesList->getThemesArray();
  }

  protected function getUserRole() {
    if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
      return 'IS_AUTHENTICATED_ANONYMOUSLY';
    } else {
      $user = $this->getUser();
      $roles = $user->getRoles();
      return $roles[0];
    }
  }

  /**
   * 
   * @param integer $theme_id
   */
  protected function getThemeFullDirName($theme_id) {
    $dbm = $this->getDoctrine()->getManager();
    $path = '';

    do {
      /* @var $theme Theme */
      $theme = $dbm->getRepository("BigElibreBundle:Theme")->find($theme_id);
      if (!$theme) {
        break;
      }
      $path = DIRECTORY_SEPARATOR . $theme->getDirName() . $path;
      $theme_id = $theme->getParentId();
    } while ($theme_id > 0);

    return $path;
  }
  
}
