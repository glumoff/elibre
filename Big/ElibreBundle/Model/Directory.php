<?php

namespace Big\ElibreBundle\Model;

/**
 * Description of Directory
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
class Directory extends File {

  public function getType() {
    return 'dir';
  }  
  
}
