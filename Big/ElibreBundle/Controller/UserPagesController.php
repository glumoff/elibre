<?php

namespace Big\ElibreBundle\Controller;

/**
 * Description of UserPagesController
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
use Big\ElibreBundle\db\ElibreDBDelegate;
use Symfony\Component\HttpFoundation\Response;
use Big\ElibreBundle\Utils\FSHelper;

class UserPagesController extends DefaultController {

  public function favoritesAction($action) {
    return $this->render('BigElibreBundle:Default:newdocs.html.twig', $this->getTemplateParams());
  }

  public function historyAction() {
    return $this->render('BigElibreBundle:Default:history.html.twig', $this->getTemplateParams());
  }

  protected function getTemplateParams() {
//    $this->get('translator')->setLocale($this->getRequest()->getPreferredLanguage());
    parent::getTemplateParams();
    $this->templateParams['results'] = $this->getHistoryDocs();
    return $this->templateParams;
  }

  protected function getHistoryDocs() {
    $em = $this->getDoctrine()->getManager();
    $query = $em->createQuery(
                    'SELECT h
                      FROM BigElibreBundle:History h
                      ORDER BY h.dt DESC'
            );
//            )->setMaxResults(10);
//WHERE create_dt + INTERVAL 30 DAY > CURRENT_DATE
    $docs = $query->getResult();
//    var_dump($docs[0]->getDocument()->getId());
    return $docs;
  }

}
