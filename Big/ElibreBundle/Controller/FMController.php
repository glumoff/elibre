<?php

/**
 * File manager backend
 *
 * @author glumoff
 */

namespace Big\ElibreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\File\File;
use Big\ElibreBundle\Model\File;
use Big\ElibreBundle\Model\Directory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FMController extends Controller {

  var $rootDir = "";

  public function indexAction($action) {
    $this->rootDir = $this->container->getParameter('big_elibre.uploadDir');
//    $response = new Response();
    $request = $this->getRequest();
    $path = $request->query->get('path');
    if (!$path) {
      $path = DIRECTORY_SEPARATOR;
    }

    switch ($action) {
      case 'getlist':
        $response = new Response(json_encode($this->getFileList($path)));
//        $response = new Response(var_export($this->getFileList($path), TRUE));
        break;
      case 'upload':
        $res = $this->handleUpload();
        $response = new Response($res);
//        $response = new Response(var_export($this->getFileList($path), TRUE));
        break;

      default:
        $response = $this->render('BigElibreBundle:fm:fm.html.twig', array('path' => $this->getRootRelativePath($path)));
        break;
    }


//    $respStr = "<pre>" . var_export($this->getFileList($path), TRUE) . "</pre>";
//    $response = $this->render('BigElibreBundle:fm:fm.html.twig', array('fileList' => $this->getFileList($path)));
//    $response->setContent($respStr);

    return $response;
  }

  function getFileList($path) {
    $path = DIRECTORY_SEPARATOR . $path;
    // sorting file and directories separately
    $dlist = array();
    $flist = array();
    foreach (glob($this->rootDir . $path . "*") as $fname) {
      if (is_dir($fname)) {
        $f = new Directory($this->getRootRelativePath($fname));
        $dlist[] = $f->toArray();
      } else {
        $f = new File($this->getRootRelativePath($fname));
        $flist[] = $f->toArray();
      }
    }
    return array_merge($dlist, $flist);
  }

  public function getRootRelativePath($path) {
    $res = $path;
    if (substr($path, 0, strlen($this->rootDir)) == $this->rootDir) {
      $res = substr($path, strlen($this->rootDir));
      if ($res[0] == DIRECTORY_SEPARATOR) {
        $res = substr($res, 1);
      }
    }
    return $res;
  }

  public function handleUpload() {
    $request = $this->getRequest();
    $path = $request->request->get('path');
    $str = '';
//    $str = var_export($request, TRUE);
    /* @var $curFile UploadedFile */
    foreach ($request->files as $curFile) {
      $name = $curFile->getClientOriginalName();
      if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
        $name = mb_convert_encoding($name, "windows-1251", "utf-8");
      }

      $str .= "PATH=" . $path;
      $f = $curFile->move($this->rootDir . $path, $name);
    }
    return $str;
  }

}
