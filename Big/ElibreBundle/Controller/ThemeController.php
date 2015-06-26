<?php

/**
 * Description of themeController
 *
 * @author glumoff
 */

namespace Big\ElibreBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Big\ElibreBundle\db\ElibreDBDelegate;

class ThemeController extends DefaultController {

  public function showThemeAction() {
    return $this->render('BigElibreBundle:Default:theme.html.twig', $this->getTemplateParams());
  }

  protected function getTemplateParams() {
    parent::getTemplateParams();
    $request = $this->getRequest();

    $db = new ElibreDBDelegate();
    // TODO: make escaping of params
    $selectedThemeCode = $request->attributes->get('theme_code', 'not set');
    $selectedTheme = $db->getThemeByCode($selectedThemeCode, $this->getUserRole());

    if ($selectedTheme) {
      $wholeThemesTree = $db->getThemes();
      $themePath = $wholeThemesTree->getThemePath($selectedThemeCode);
      //$activeThemeRoot = $themePath
//    echo "<pre>" . var_export($themePath, TRUE) . "+</pre>";

      $subthemesList = $db->getSubThemes($selectedThemeCode, $this->getUserRole());
      $documentsList = $db->getDocuments($selectedTheme->getID(), $this->getUserRole());
//    var_dump($documentsList);
//    $this->templateParams['action'] = $request->attributes->get('action', 'not set');
      $this->templateParams['selectedTheme'] = $selectedTheme;
      $this->templateParams['activeThemeRoot'] = (count($themePath) > 0) ? $themePath[max(count($themePath) - 1, 0)]->getID() : $selectedTheme->getID();
      $this->templateParams['activeThemeRoot2'] = (count($themePath) > 1) ? $themePath[max(count($themePath) - 2, 0)]->getID() : $selectedTheme->getID();
      $this->templateParams['subthemes'] = $subthemesList->getThemesArray();
      $this->templateParams['documents'] = $documentsList->getDocsArray();
      $this->templateParams['fileList'] = $this->getFileList($selectedTheme);
      
//      echo "<pre>";
//      var_dump($this->templateParams['fileList']);
//      echo "</pre>";
//    var_dump($this->templateParams['activeThemeRoot2']);
    }
    return $this->templateParams;
  }

  /**
   * 
   * @param Theme $theme
   * @return array
   */
  private function getFileList($theme) {
    if (!$theme) {
      return array();
    }
    $rootDir = $this->container->getParameter('big_elibre.rootDir');
    $path = $this->getThemeFullDirName($theme->getId()) . DIRECTORY_SEPARATOR;
    $flist = array();
    foreach (glob($rootDir . $path . "*") as $fname) {
      if (is_file($fname)) {
        $fname = basename($fname);
        $f = new \Big\ElibreBundle\Model\File($fname);
        $flist[$fname] = $f->toArray();
      }
    }
    return $flist;
  }

}
