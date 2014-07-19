<?php

namespace Big\ElibreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
//use Big\ElibreBundle\Utils\FSHelper;

class TestController extends Controller {

  public function indexAction() {
    $str = 'test';

//    $str = $this->container->getParameter('big_elibre.rootDir');
//    $str .= "<br>";
//    $str .= $this->container->getParameter('big_elibre.uploadDir');
//
//    
//    $themeDocPath = "/mnt/path/сіві ві пр обелом.doc";
//    $str .= "<hr>";
//    $str .= "themeDocPath=" . var_export($themeDocPath, TRUE);
//    $str .= "<br>basename=" . var_export(basename($themeDocPath), TRUE);
//    $str .= "<br>FSHelper::getBaseName=" . var_export(FSHelper::getBaseName($themeDocPath), TRUE);

    $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('send@example.com')
            ->setTo('glumoff@ukr.net')
            ->setBody('swiftmailer test message body (' . date('d.m.Y, H:i:s') . ')');
    $this->get('mailer')->send($message);

    $response = new Response($str);
    return $response;
  }

}
