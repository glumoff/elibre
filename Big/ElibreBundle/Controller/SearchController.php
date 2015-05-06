<?php

namespace Big\ElibreBundle\Controller;

/**
 * Description of SearchController
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Big\ElibreBundle\db\ElibreDBDelegate;

class SearchController extends Controller {

  protected $templateParams = NULL;

  public function indexAction() {
    $response = $this->render('BigElibreBundle:Default:search.html.twig', $this->getSearchParams());
    return $response;
  }

  public function newDocsAction() {
    $response = $this->render('BigElibreBundle:Default:newdocs.html.twig', $this->getNewDocsParams());
    return $response;
  }

  protected function getTemplateParams() {
//    $this->get('translator')->setLocale($this->getRequest()->getPreferredLanguage());

    if ($this->templateParams === NULL) {
      $this->templateParams = array();
      $this->templateParams['menuThemes'] = $this->getThemesMenuArray();
    }
    return $this->templateParams;
  }

  protected function getThemesMenuArray() {
    $db = new ElibreDBDelegate();
    $themesList = $db->getThemes(2, $this->getUserRole());
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

  protected function getResults($needle) {
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
                    'SELECT d
                      FROM BigElibreBundle:Document d
                      WHERE d.title LIKE :needle
                         OR d.tags LIKE :needle
                         OR d.path LIKE :needle
                      ORDER BY d.title ASC'
            )->setParameter('needle', '%' . $needle . '%');
    $docs = $query->getResult();
    return $docs;
  }

  protected function getNewDocs() {
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
                    'SELECT d
                      FROM BigElibreBundle:Document d
                      ORDER BY d.create_dt DESC'
//            );
            )->setMaxResults(10);
//WHERE create_dt + INTERVAL 30 DAY > CURRENT_DATE
    $docs = $query->getResult();
    return $docs;
  }

  public function getSearchParams() {
    $this->templateParams = $this->getTemplateParams();
    $needle = $this->getRequest()->query->get('needle');
    $this->templateParams['needle'] = $needle;
    if ($needle) {
      $this->templateParams['results'] = $this->getResults($needle);
    }
    return $this->templateParams;
  }

  public function getNewDocsParams() {
    $this->templateParams = $this->getTemplateParams();

//    $needle = $this->getRequest()->query->get('needle');
//    $this->templateParams['needle'] = $needle;
//    if ($needle) {
    $this->templateParams['results'] = $this->getNewDocs();
//    }
    return $this->templateParams;
  }

}
