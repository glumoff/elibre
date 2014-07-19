<?php

namespace Big\ElibreUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BigElibreUserBundle extends Bundle {

  public function getParent() {
    return 'FOSUserBundle';
  }

}
