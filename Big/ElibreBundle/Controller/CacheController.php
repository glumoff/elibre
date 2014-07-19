<?php

/**
 * Description of CacheController
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */

namespace Big\ElibreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Symfony\Component\HttpFoundation\Response;

class CacheController extends Controller {

  public function indexAction($action) {
    $response = new Response();
    
    $respStr = 'cache::'.$action.'<br>';
    
    if ($action == 'clear') {
      $cmd = new CacheClearCommand();
      $respStr .= '<pre>'.$cmd->getHelp().'</pre>';
      
    }    

    $response->setContent($respStr);
    
    return $response;
  }

}
