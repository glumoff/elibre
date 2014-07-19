<?php

namespace Big\ElibreBundle\Model;

use Big\ElibreBundle\Utils\FSHelper;

/**
 * Description of File
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
class File {

  private $fpath;

  public function __construct($fpath = NULL) {

    if ($fpath) {
      $fpath = FSHelper::fixOSFileName($fpath, TRUE);
    }

    $this->setFilePath($fpath);
  }

  public function getFilePath() {
    return $this->fpath;
  }

  public function setFilePath($fpath) {
    $this->fpath = $fpath;
    return $this;
  }

  public function getFileName() {
//    return basename('' . $this->fpath);
    $a = explode(DIRECTORY_SEPARATOR, $this->fpath);
//    $str = end($a);
    return end($a);
//    return $str;
  }

  public function getType() {
    return 'file';
  }

  public function toArray() {
    return array(
        'fpath' => $this->getFilePath(),
        'fname' => $this->getFileName(),
        'type' => $this->getType()
    );
//    return (array) $this;
  }

}
