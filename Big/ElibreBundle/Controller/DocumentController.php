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
        

    switch ($action) {
      case 'download':
        /* @var $hm HistoryManager */
        $hm = $this->get('history_manager');
        $doc = $this->getSelectedDoc();
        $hm->saveHistory($this->getUser(), $doc);
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
      }

      $this->templateParams['doc'] = $doc;
      $this->templateParams['selectedTheme'] = $selectedTheme;
      if ($themePath) {
        $this->templateParams['activeThemeRoot'] = (count($themePath) > 0) ? $themePath[max(count($themePath) - 1, 0)]->getID() : '-1';
        $this->templateParams['activeThemeRoot2'] = (count($themePath) > 1) ? $themePath[max(count($themePath) - 2, 0)]->getID() : $selectedTheme->getID();
//        var_dump($this->templateParams['activeThemeRoot']);
//        var_dump($this->templateParams['activeThemeRoot2']);
      }
    }
    return $this->templateParams;
  }

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

  /**
   * 
   * @param integer $theme_id
   */
  private function getThemeFullDirName($theme_id) {
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
