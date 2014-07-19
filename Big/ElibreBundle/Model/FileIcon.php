<?php

namespace Big\ElibreBundle\Model;

/**
 * Description of FileIcon
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
class FileIcon {
  
  private $ICONS_DIR;
//  private $ICONS_DIR_FOR_LINK = './images/icons/';
  private $ICONS_DIR_FOR_LINK = '';

  private $iconPath;
  private $fileExtension;



  public function __construct($fileExtension = 'unknown') {
    $this->ICONS_DIR = dirname(dirname(__FILE__)).'/Resources/public/images/icons/';
    $this->fileExtension = $fileExtension;
  }
  
  function getIcon() {
    if (!isset($this->iconPath)) {
//      echo $this->ICONS_DIR . $this->fileExtension . '.png+<br>';
      if (!file_exists($this->ICONS_DIR . $this->fileExtension . '.png')) {
        $this->fileExtension = 'unknown';
      }
      $this->iconPath = $this->ICONS_DIR_FOR_LINK . $this->fileExtension . '.png';
    }
    return $this->iconPath;
  }

}
