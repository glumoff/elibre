<?php

namespace Big\ElibreBundle\Controller;

/**
 * Description of DocumentController
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
use Big\ElibreBundle\db\ElibreDBDelegate;
use Symfony\Component\HttpFoundation\Response;
use Big\ElibreBundle\Utils\FSHelper;
use Big\ElibreBundle\Utils\HistoryManager;

class DocumentController extends DefaultController {

  //TODO: move it to parameters
  private $docs_path = '';

  public function showDocAction($action) {
    $this->docs_path = $this->container->getParameter('big_elibre.rootDir');
    //action: view|download|edit
    //update user`s history
    $doc = $this->getSelectedDoc();
    if ($doc) {
      $role = $this->getUserRole();
      if ($role && (($role == 'ROLE_USER') || ($role == 'IS_AUTHENTICATED_ANONYMOUSLY'))) {
        if (!$doc->getTheme()->isPublic()) {
          throw $this->createNotFoundException('The document does not exist');
        }
      }
    }

    switch ($action) {
      case 'download':
        /* @var $hm HistoryManager */
        if ($doc) {
          $hm = $this->get('history_manager');
          $hm->saveHistory($this->getUser(), $doc);
        }
        return $this->doDownloadFile($doc);
        break;

      default:
        return $this->render('BigElibreBundle:Default:doc.html.twig', $this->getTemplateParams());
//        break;
    }
  }

  protected function getTemplateParams() {
    if ($this->templateParams === NULL) {
      parent::getTemplateParams();

      $doc = $this->getSelectedDoc();
      if ($doc) {
//        echo __CLASS__ . '.' . __FUNCTION__ . ' #' . __LINE__ . ": i was here<br>";
//        $selectedTheme = $db->getTheme($doc->getTheme()->getId());
        $selectedTheme = $doc->getTheme();
//        var_dump($selectedTheme->getCode());
        if ($selectedTheme) {
          $db = new ElibreDBDelegate();
          $wholeThemesTree = $db->getThemes();
          $themePath = $wholeThemesTree->getThemePath($selectedTheme->getCode());
//          echo "<pre>";
//          var_export($themePath);
//          echo "</pre>";
        }
        $themeFullPath = $this->getThemeFullDirName($doc->getTheme()->getId());
        $fname = $this->docs_path . DIRECTORY_SEPARATOR . $themeFullPath . DIRECTORY_SEPARATOR . $doc->getPath();
        $fname_enc = FSHelper::fixOSFileName($fname);
        $this->templateParams['docFullPathDir'] = $fname_enc;
      }

      $this->templateParams['doc'] = $doc;
      $this->templateParams['selectedTheme'] = $selectedTheme;


      if ($themePath) {
        $this->templateParams['activeThemeRoot'] = (count($themePath) > 0) ? $themePath[max(count($themePath) - 1, 0)]->getID() : '-1';
        $this->templateParams['activeThemeRoot2'] = (count($themePath) > 1) ? $themePath[max(count($themePath) - 2, 0)]->getID() : $selectedTheme->getID();
//        $this->templateParams['activeThemeRoot2'] = '-1';
//        var_dump($this->templateParams['activeThemeRoot']);
//        var_dump($this->templateParams['activeThemeRoot2']);
      }
    }
    return $this->templateParams;
  }

  /**
   * 
   * @param \Big\ElibreBundle\Entity\Document $doc
   * @return Response
   * @throws type
   */
  protected function doDownloadFile($doc = NULL) {
//    return 
//    return;    
//          $response = new Response("");
//      return $response;
    if (!$doc) {
      $doc = $this->getSelectedDoc();
    }
    if ($doc) {
      $themePath = $this->getThemeFullDirName($doc->getTheme()->getId());
      $fname = $this->docs_path . DIRECTORY_SEPARATOR . $themePath . DIRECTORY_SEPARATOR . $doc->getPath();
      $fname_enc = FSHelper::fixOSFileName($fname);

      if (file_exists($fname_enc) && !is_dir($fname_enc)) {
        $response = new Response();
        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($fname_enc));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . FSHelper::getBaseName($fname) . '"');
        $response->headers->set('Content-length', filesize($fname_enc));
        // Send headers before outputting anything
        $response->sendHeaders();
        $response->setContent(readfile($fname_enc));
        return $response;
      } else {
        throw $this->createNotFoundException('The document does not exist');
      }
    } else {
      throw $this->createNotFoundException('The document does not selected');
    }
  }

  private function getSelectedDoc() {
    $request = $this->getRequest();

    $dbm = $this->getDoctrine()->getManager();

//    $db = new ElibreDBDelegate();
    // TODO: make escaping of params
    $selectedDocID = $request->attributes->get('doc_id', 'not set');
//    $doc = $db->getDocument($selectedDocID);
    $doc = $dbm->getRepository("BigElibreBundle:Document")->findOneById($selectedDocID);
    return $doc;
  }

}
