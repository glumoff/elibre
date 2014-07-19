<?php

namespace Big\ElibreBundle\Model;

use Big\ElibreBundle\Entity\Document;

/**
 * Description of DocumentsList
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
class DocumentsList {
  private $docsArr;
  
  /**
   * 
   * @param Document $doc
   */
  public function addDoc($doc) {
    $this->docsArr[$doc->getID()] = $doc;
  }
  
  public function getDocsArray() {
    return $this->docsArr;
  }
  
}
