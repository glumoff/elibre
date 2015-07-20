<?php

namespace Big\ElibreBundle\Controller;

/**
 * Description of AdminController
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Big\ElibreBundle\Entity\Theme;
use Big\ElibreBundle\Entity\User;
use Big\ElibreBundle\Entity\Document;
use Big\ElibreBundle\Utils\FSHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminController extends Controller {

  var $rootDir = "";
  var $uploadDir = "";

  public function indexAction($mode, $action) {
    $this->rootDir = $this->container->getParameter('big_elibre.rootDir');
    $this->uploadDir = $this->container->getParameter('big_elibre.uploadDir');

    $request = $this->getRequest();
//    $str = "<pre>";
    switch ($mode) {
      case 'themes':
        if (($action == 'edit') || ($action == 'save') || ($action == 'active') || ($action == 'delete') || ($action == 'public')) {
          $request = $this->getRequest();
          $dbm = $this->getDoctrine()->getManager();
          $formArr = $request->request->get('form');
          $themeID = $formArr["id"] ? $formArr["id"] : $request->query->get('theme');
//          $str .= "themeID: " . var_export($request->request, TRUE) . "\n";
//          $str .= "themeID: " . var_export($themeID, TRUE) . "\n";
          if ($themeID) {
            $theme = $dbm->getRepository("BigElibreBundle:Theme")->find($themeID);
//            $str .= "found in DB: " . var_export($theme, TRUE) . "\n";
          } else {
            $theme = new Theme();
            $theme->setTitle('New theme');
          }
          /* @var $form \Symfony\Component\Form\Form */
          $form = $this->createFormBuilder($theme)
                  ->setAction($this->generateUrl('big_elibre_admin', array('mode' => $mode,
                              'action' => 'save')))
                  ->add('id', 'hidden')
                  ->add('parent_id', 'hidden')
                  ->add('title', 'text')
                  ->add('code', 'text')
                  ->add('descr', 'textarea', array('required' => FALSE))
//                  ->add('show_order', 'hidden')''
//                  ->add('picture', 'file', array(
//                      'data_class' => null,
//                      'mapped' => FALSE,
//                      'required' => FALSE
//                  ))
                  ->add('save', 'submit')
                  ->getForm();

          if ($action == 'edit') {
            $response = $this->render('BigElibreBundle:admin:editTheme.html.twig', array(
                'editForm' => $form->createView()
                    )
            );
          } elseif ($action == 'save') {
//            $str = "<pre>before handleRequest: " . var_export($theme, TRUE) . "\n";
            $parentPath = $this->rootDir . $this->getThemeFullDirName($theme->getParentId());
            $oldDirName = $parentPath . DIRECTORY_SEPARATOR . $theme->getDirName();
            $form->handleRequest($request);
            $newDirName = $parentPath . DIRECTORY_SEPARATOR . $theme->getDirName();

            $this->updateThemeDir($oldDirName, $newDirName);

//            if ($form->isValid()) {
//            $str .= "after handleRequest: " . var_export($theme, TRUE) . "\n";
//            $response = new \Symfony\Component\HttpFoundation\Response($str);
            $res = $dbm->persist($theme);
            $dbm->flush();
            return $this->redirect($this->generateUrl('big_elibre_admin', array('mode' => $mode,
                                'action' => 'view')));
          } elseif ($action == 'active') {
            $theme->setIsActive(!$theme->isActive());
            $res = $dbm->persist($theme);
            $dbm->flush();
            return $this->redirect($this->generateUrl('big_elibre_admin', array('mode' => $mode,
                                'action' => 'view')));
          } elseif ($action == 'public') {
            $theme->setIsPublic(!$theme->isPublic());
            $res = $dbm->persist($theme);
            $dbm->flush();
            return $this->redirect($this->generateUrl('big_elibre_admin', array('mode' => $mode,
                                'action' => 'view')));
          } elseif ($action == 'delete') {
            $dbm->remove($theme);
            $dbm->flush();
            return $this->redirect($this->generateUrl('big_elibre_admin', array('mode' => $mode,
                                'action' => 'view')));
          }
        } elseif ($action == 'savebatch') {
          //$str = "_POST = " . var_export($_POST, true);
          $request = $this->getRequest();
          $actions = $request->request->get('actions');
          $str = $this->saveBatch($actions);
          $response = new \Symfony\Component\HttpFoundation\Response($mode . '/' . $action . '\n' . $str);
        } else {
          $request = $this->getRequest();
          $response = $this->render('BigElibreBundle:admin:themes.html.twig', array(
              'themesJSON' => $this->getThemesJSON(),
              'isAjax' => $request->isXmlHttpRequest()
//              'isAjax' => FALSE
                  )
          );
        }
        break;

      case 'docs':
        $request = $this->getRequest();
        $dbm = $this->getDoctrine()->getManager();
        $formArr = $request->request->get('form');
        $docID = $formArr["id"] ? $formArr["id"] : $request->query->get('doc');

        $themeCode = $request->query->get('theme');
        $theme = $dbm->getRepository("BigElibreBundle:Theme")->findOneByCode($themeCode);

        if ($docID) {
          $doc = $dbm->getRepository("BigElibreBundle:Document")->find($docID);
        } else {
          $doc = new Document();
//          $doc->setTitle('New document');
//          return new \Symfony\Component\HttpFoundation\Response("request: " . var_export($_SERVER, TRUE));
          //exit();
//          $theme = $dbm->getRepository("BigElibreBundle:Theme")->findOneByCode($themeCode);
//          return new \Symfony\Component\HttpFoundation\Response("theme: " . var_export($theme, TRUE));
//          var_dump($themeCode);
          if ($theme) {
            $doc->setTheme($theme);
          }
        }
//          $str = "before handleSaveDoc: " . var_export($docID, TRUE) . "\n";
//          echo $str;

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createFormBuilder($doc)
                ->setAction($this->generateUrl('big_elibre_admin', array('mode' => $mode,
                            'action' => 'save')))
                ->add('id', 'hidden')
//                ->add('theme', 'text')
                ->add('theme', new \Big\ElibreBundle\Form\ThemeType())
                ->add('title', 'text')
                ->add('path', 'text')
                ->add('annotation', 'textarea', array('required' => FALSE))
                ->add('tags', 'text')
                ->add('picture', 'file', array(
                    'data_class' => null,
                    'mapped' => FALSE,
                    'required' => FALSE
                ))

//                ->add('theme', 'hidden')
//                  ->add('show_order', 'hidden')
                ->add('save', 'submit')
                ->getForm();

        if (($action == 'add') || ($action == 'edit')) {
//          var_dump($doc);
//          exit;
          $response = $this->render('BigElibreBundle:admin:addDoc.html.twig', array(
              'themesJSON' => $this->getThemesJSON(),
              'isAjax' => $request->isXmlHttpRequest(),
              'theme' => $theme,
//              'doc' => $doc,
              'addDocForm' => $form->createView()
//            'isAjax' => FALSE
                  )
          );
        } elseif ($action == 'save') {
          return $this->handleSaveDoc($form, $doc);
        } elseif ($action == 'del') {
          $leaveFiles = $formArr["leave"] ? $formArr["leave"] : $request->query->get('leave');
          return $this->deleteDoc($doc, $leaveFiles);
        } elseif ($action == 'auto') { // add docs from theme dir automatically
          $response = $this->render('BigElibreBundle:admin:autoAdd.html.twig', array(
              'isAjax' => $request->isXmlHttpRequest(),
              'theme' => $theme,
                  )
          );
        } elseif ($action == 'autolist') { // get list of files not in db
          $response = new JsonResponse($this->getAutoList($theme));
        } elseif ($action == 'autosave') { // add automatically files as docs
          return $this->autoAddSave($theme);
        } else {
          $response = $this->render('BigElibreBundle:admin:index.html.twig', array('content' => $mode));
        }
        break;

      case 'user':
        /* @var $um \FOS\UserBundle\Model\UserManager */
        $um = $this->container->get('fos_user.user_manager');
        if ($action == 'list') {
          $dbm = $this->getDoctrine()->getManager();
          $users = $dbm->getRepository("BigElibreBundle:User")->findAll();
          $response = new JsonResponse($this->usersToJTable($users));
        } elseif (($action == 'create') || ($action == 'update')) {
          $isNewUser = (!$request->get('UserId'));
          $newIsEnabled = $request->get('isEnabled', FALSE);
          $sendNotify = $request->get('sendMail', FALSE);
          $newUsername = $request->get('Name');
          $newPassword = $request->get('Password');
          $newRole = $request->get('Role');
          if ($newIsEnabled) {
            $actionMessage = 'register.account_activated';
          } else {
            $actionMessage = 'register.account_deactivated';
          }
          if (!$isNewUser) {
            $oldUser = $um->findUserBy(array('id' => $request->get('UserId')));
            if ($oldUser && ($newIsEnabled == $oldUser->isEnabled())) {
              $sendNotify = FALSE;
            }
          }
          /* @var $editUser \Big\ElibreBundle\Entity\User */
          try {
            if ($isNewUser) {
              $editUser = $um->createUser();
            } else {
              $editUser = $um->findUserBy(array('id' => $request->get('UserId')));
              if (!$editUser) {
                throw new \Exception('User not found');
              }
            }

            $res = array();
            $editUser->setUsername($request->get('Name'));
            $editUser->setEmail($request->get('Email'));
            $editUser->setRoles([$this->formatUserRoleFromJTable($newRole)]);
            $password = $request->get('Password');
            if ($password) {
              $editUser->setPlainPassword($password);
            }
            $editUser->setEnabled($request->get('isEnabled', FALSE));

            if ($isNewUser) {
              if ($um->findUserByUsername($editUser->getUsername())) {
                throw new \Exception('Username already exists');
              }
              if ($um->findUserByEmail($editUser->getEmail())) {
                throw new \Exception('Email already used');
              }
            }
            if ($request->get('Password') != $request->get('PasswordRetype')) {
              throw new \Exception('Passwords mismatch');
            }
            $um->updateUser($editUser);

            $res['Result'] = "OK";
            $res['Record'] = array('UserId' => $editUser->getId(),
                'Name' => $editUser->getUsername(),
                'Email' => $editUser->getEmail(),
                'Role' => $this->formatUserRoleForJTable($editUser),
                'isEnabled' => $editUser->isEnabled());

            if ($sendNotify) {
              $this->sendNotifyUser($editUser, $actionMessage);
            }
          } catch (\Exception $exc) {
            $res['Result'] = "ERROR";
            $res['Message'] = $exc->getMessage();
          }
          $response = new JsonResponse($res);
        } elseif ($action == 'delete') {
          $res = array();
          $editUser = $um->findUserBy(array('id' => $request->get('UserId')));
          if ($editUser) {
            $um->deleteUser($editUser);
            $res['Result'] = "OK";
          }
          $response = new JsonResponse($res);
        } else {
          $response = $this->render('BigElibreBundle:admin:users.html.twig', array());
        }
        break;

      default :
        $response = $this->render('BigElibreBundle:admin:index.html.twig', array('content' => '<-- choose menu item'));
    }
    //$response = new \Symfony\Component\HttpFoundation\Response("Mode: " . $mode);
    return $response;
  }

  private function getThemesJSON() {
    $dbm = $this->getDoctrine()->getManager();

//    $themes = $dbm->getRepository("BigElibreBundle:Theme")->findAll();
    $themes = $dbm->getRepository("BigElibreBundle:Theme")->findBy(array(), array('parentId' => 'ASC',
        'showOrder' => 'ASC',
        'id' => 'ASC'));

    $themes_arr = array();
    if (is_array($themes)) {
      /* @var $theme Theme */
      foreach ($themes as $theme) {
        $themes_arr[] = array('id' => $theme->getId(),
            'parent' => ($theme->getParentId()) ? $theme->getParentId() : '#',
            'text' => $theme->getTitle(),
            'code' => $theme->getCode(),
            'active' => $theme->isActive(),
            'public' => $theme->isPublic(),
        );
      }
    }

    return json_encode($themes_arr);
  }

  private function saveBatch($actions) {
    $res = "";
    if (is_array($actions)) {
      $dbm = $this->getDoctrine()->getManager();
      foreach ($actions as $act) {
        $res .= var_export($act, true);
        $themeID = $act['modifiedThemeID'];
        $newParentID = ($act['moveNewParentID'] != '#') ? $act['moveNewParentID'] : 0;
        $oldParentID = ($act['moveOldParentID'] != '#') ? $act['moveOldParentID'] : 0;
        $newOrder = $act['moveNewOrder'];
        $res .= "---newParentID=" . $newParentID;
        $res .= "---themeID=" . $themeID;
//        echo "---newParentID=".$newParentID;
        if ($themeID) {
//          $dbm->flush();

          if ($newParentID != $oldParentID) {
            /* @var $theme Theme */
            $theme = $dbm->getRepository("BigElibreBundle:Theme")->find($themeID);
            $theme->setParentId($newParentID);
            $theme->setShowOrder($newOrder);
          }

          $themes = $dbm->getRepository("BigElibreBundle:Theme")->findBy(
                  array('parentId' => $newParentID), array('showOrder' => 'ASC', 'id' => 'ASC'));

          if (is_array($themes)) {
            $i = 0;
            foreach ($themes as $cur_theme) {
              $dbm->persist($cur_theme);
//              echo "---".$cur_theme->getTitle()." i=" . $i;
              /* @var $cur_theme Theme */
              if ($cur_theme->getId() == $themeID) {
                $cur_theme->setParentId($newParentID);
                $cur_theme->setShowOrder($newOrder);
//              $dbm->persist($cur_theme);
//                echo "---".$cur_theme->getTitle()." newOrder=" . $newOrder;
//                $dbm->persist($cur_theme);
                continue;
              }
              if ($i == $newOrder) {
                $i++;
              }
//              echo "---".$cur_theme->getTitle()." i=" . $i;
              $cur_theme->setShowOrder($i);
              $i++;
            }
          }
        }
        // apply changes to filesystem
        /* @var $theme Theme */
        $theme = $dbm->getRepository("BigElibreBundle:Theme")->find($themeID);
        if ($theme) {
          $oldParentPath = $this->rootDir . $this->getThemeFullDirName($oldParentID);
          $newParentPath = $this->rootDir . $this->getThemeFullDirName($newParentID);
          $this->updateThemeDir($oldParentPath . DIRECTORY_SEPARATOR . $theme->getDirName(), $newParentPath . DIRECTORY_SEPARATOR . $theme->getDirName());
        }
      }
      $dbm->flush();
    }
    return $res;
  }

  /**
   * 
   * @param string $oldDirName
   * @param string $newDirName
   * @param Theme $theme
   */
  private function updateThemeDir($oldDirName, $newDirName) {
//    echo __FUNCTION__ . ', line ' . __LINE__ . "<br>";
//    var_dump($this->getThemeFullDirName($theme->getId()));
//    $newFullPath = $this->rootDir . $this->getThemeFullDirName($theme->getId());
//    $parentPath = dirname($newFullPath);
//    $oldFullPath = $parentPath . $oldDirName;
//    $newFullPath = $parentPath . $newDirName;

    $oldDirName = FSHelper::fixOSFileName($oldDirName);
    $newDirName = FSHelper::fixOSFileName($newDirName);

    if (!is_dir($oldDirName)) {
      if (!is_dir($newDirName)) {
        mkdir($newDirName, 0775, TRUE);
      }
    } else {
      if (!is_dir($newDirName)) {
        $parentDir = dirname($newDirName);
        if (!is_dir($parentDir)) {
          mkdir($parentDir, 0775, TRUE);
        }
        if ($oldDirName != $newDirName) {
          rename($oldDirName, $newDirName);
        }
      } else {
        // todo: exeption here 
      }
    }

//    exit;
    //if (file)
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

  /**
   * 
   * @param type $form
   * @param Document $doc
   * @return \Symfony\Component\HttpFoundation\Response
   */
  protected function handleSaveDoc($form, $doc) {
    $request = $this->getRequest();

    $dbm = $this->getDoctrine()->getManager();
//    $str = "<pre>";
//    $str .= "before handleRequest: " . var_export($doc, TRUE) . "\n";
//    $str .= "before handleRequest: " . var_export($doc->getCreateDt(), TRUE) . "\n";
    $form->handleRequest($request);

//          var_dump($doc->getPicture());

    if (!$doc->getTheme() || !$doc->getTheme()->getCode()) {
      $theme = $dbm->getRepository("BigElibreBundle:Theme")->findOneById($doc->getTheme()->getId());
      $doc->setTheme($theme);
//      exit;
    }

    if ($doc->getCreateDt()) {
      $doc->setCreateDt(new \DateTime());
    }
    //if ($doc->getEditDt()) {
    $doc->setEditDt(new \DateTime());
    //}
//    //$this->updateThemeDir($oldDirName, $newDirName);
////            if ($form->isValid()) {
    // move file from upload dir to theme dir
    $moved = FALSE;

//    $str = '<pre>';
//    $str .= $doc->getPath()."\n";
    if ($doc->getPath()) {
      $uploadedDocPath = $this->uploadDir . DIRECTORY_SEPARATOR . $doc->getPath();
      $themePath = $this->rootDir . $this->getThemeFullDirName($doc->getTheme()->getId());
      $themeDocPath = $themePath . DIRECTORY_SEPARATOR . FSHelper::getBaseName($doc->getPath());
      $this->updateThemeDir($themePath, $themePath);
//      $str .= $uploadedDocPath . " - ";
//      $str .= var_export(file_exists($uploadedDocPath), TRUE) . "\n";
      $uploadedDocPath_enc = FSHelper::fixOSFileName($uploadedDocPath);
      $themePath_enc = FSHelper::fixOSFileName($themePath);
      $themeDocPath_enc = FSHelper::fixOSFileName($themeDocPath);
      if (file_exists($uploadedDocPath_enc) && file_exists($themePath_enc)) {
//        $str .= "Both path are exist\n";
        $moved = rename($uploadedDocPath_enc, $themeDocPath_enc);
      }
//      $str .= $themePath . " - ";
//      $str .= var_export(file_exists($themePath), TRUE) . "\n";
//      $str .= $themeDocPath . "\n";
//      $str .= "move uploaded = " . var_export($moved, TRUE) . "\n";
    }

    if ($moved) {
      $doc->setPath(FSHelper::getBaseName($themeDocPath));

      $finfo = \finfo_open(FILEINFO_MIME_TYPE);
      $doc->setMimeType(\finfo_file($finfo, $themeDocPath_enc));
      \finfo_close($finfo);
    }

    if ($form['picture']->getData()) {
      /* @var $file UploadedFile */
      $file = $form['picture']->getData();
      // TODO: move hardcoded max file size to params
      if ($file && file_exists($file->getPathname()) && ($file->getSize() <= 819200)) {
        $doc->setPictureMimeType($file->getMimeType());
        $doc->setPicture(file_get_contents($file->getPathname()));
      }
    }

    $dbm->persist($doc->getTheme());
    $dbm->persist($doc);
    $dbm->flush();
//    $theme = $dbm->getRepository("BigElibreBundle:Theme")->findOneById($doc->getThemeId());
    $theme = $doc->getTheme();
//    var_dump($theme->getCode());
//    exit;
    $themeCode = $theme->getCode();


//    $response = new \Symfony\Component\HttpFoundation\Response($str);
    return $this->redirect($this->generateUrl('big_elibre_theme', array('theme_code' => $themeCode)));
//    return $response;
  }

  protected function deleteDoc($doc, $leaveFiles = FALSE) {
    if ($doc && $doc->getID()) {
      $dbm = $this->getDoctrine()->getManager();
//      $theme = $dbm->getRepository("BigElibreBundle:Theme")->findOneById($doc->getThemeId());
      $theme = $doc->getTheme();
      $themeCode = $theme->getCode();

      // remove from FS
      $res = FALSE;
      if (!$leaveFiles) {
        if ($doc->getPath()) {
          $themePath = $this->rootDir . $this->getThemeFullDirName($doc->getTheme()->getId());
          $themeDocPath = $themePath . DIRECTORY_SEPARATOR . $doc->getPath();

          $themeDocPath_enc = FSHelper::fixOSFileName($themeDocPath);
          if (file_exists($themeDocPath_enc)) {
            $res = unlink($themeDocPath_enc);
          } else {
            $res = TRUE;
          }
        }
      }

      if ($res | $leaveFiles) {
        // remove from DB
        $dbm->persist($doc);
        $dbm->remove($doc);
        $dbm->flush();
      }

      return $this->redirect($this->generateUrl('big_elibre_theme', array('theme_code' => $themeCode)));
    }
  }

  protected function usersToJTable($users) {
    //return '{Result:"OK",Records:[{id:1,username:"panikovsky",email:"glumoff@gmail.com",enabled:true}]}';

    $users_arr = array();
    if (is_array($users)) {
      /* @var $user \Big\ElibreBundle\Entity\User */
      foreach ($users as $user) {
        $users_arr[] = array('UserId' => $user->getId(),
            'Name' => $user->getUsername(),
            'Email' => $user->getEmail(),
            'isEnabled' => $user->isEnabled(),
            'Role' => $this->formatUserRoleForJTable($user),
            'isConfirmed' => $user->getConfirmationToken() == '',
//            'isConfirmed' => 0,
        );
      }
    }

    $res = array();
    $res['Result'] = "OK";
    $res['Records'] = $users_arr;
//    return json_encode($res);
    return $res;
  }

  public function sendNotifyUser($user, $msgText) {
//    $this->get('translator')->trans('Symfony2 is great');
    if ($user && $user->getEmail()) {
      $translator = $this->get('translator');
      $from = array($this->container->getParameter('mailer_user') => $this->container->getParameter('mailer_from_name'));
      $message = \Swift_Message::newInstance()
              ->setSubject($translator->trans('register.account_changed'))
              ->setFrom($from)
              ->setTo($user->getEmail())
              ->setBody($translator->trans($msgText));
      $this->get('mailer')->send($message);
    }
  }

  /**
   * 
   * @param User $user
   */
  private function formatUserRoleForJTable($user) {
    $role = is_array($user->getRoles()) ? $user->getRoles()[0] : 'ROLE_USER';
    switch ($role) {
      case 'ROLE_ADMIN': return 'admin';
      case 'ROLE_TEACHER': return 'teacher';
      default: return 'user';
    }
  }

  private function formatUserRoleFromJTable($role) {
    switch ($role) {
      case 'admin': return 'ROLE_ADMIN';
      case 'teacher': return 'ROLE_TEACHER';
      default: return 'ROLE_USER';
    }
  }

  /**
   * 
   * @param Theme $theme
   * @return array
   */
  private function getAutoList($theme) {
    if (!$theme) {
      return array();
    }

    $rootDir = $this->container->getParameter('big_elibre.rootDir');

    $path = FSHelper::fixOSFileName($this->getThemeFullDirName($theme->getId()) . DIRECTORY_SEPARATOR);
	
    $dbm = $this->getDoctrine()->getManager();

    $flist = array();
    foreach (glob($rootDir . $path . "*") as $fname) {
      if (is_file($fname)) {
        $fname = basename($fname);
        $query = $dbm->createQuery(
                        'SELECT d.id
                      FROM BigElibreBundle:Document d
                      WHERE d.path = :fname
                        AND d.theme = :theme
                      ORDER BY d.title ASC'
                )->setParameter('fname', FSHelper::fixOSFileName($fname, TRUE))
                ->setParameter('theme', $theme);
        $docs = $query->getResult();
        if ($docs && is_array($docs) && (count($docs) > 0)) {
//          $f = new \Big\ElibreBundle\Model\File($fname . '+');
//          $flist[] = $f->toArray();
        } else {
          $f = new \Big\ElibreBundle\Model\File($fname);
          $flist[] = $f->toArray();
        }
      }
    }
    //exit;
    return $flist;
  }

  /**
   * 
   * @param Theme $theme
   * @return Response
   */
  private function autoAddSave($theme) {
    $request = $this->getRequest();
    $fileList = $request->request->get('flist');

    $dbm = $this->getDoctrine()->getManager();

    $rootDir = $this->container->getParameter('big_elibre.rootDir');
    //$path = FSHelper::fixOSFileName($rootDir . $this->getThemeFullDirName($theme->getId()) . DIRECTORY_SEPARATOR);
    $path = $rootDir . $this->getThemeFullDirName($theme->getId()) . DIRECTORY_SEPARATOR;

    $cnt = 0;

    if (is_array($fileList)) {
//      $debugStr = '+' . __LINE__ . '+';
      foreach ($fileList as $fname) {
//        $debugStr = '+' . __LINE__ . '+';
//        $debugStr = '+' . $dir . $fname . '+';
		//$fname = FSHelper::fixOSFileName($fname);
		//var_dump($path . $fname);
		//var_dump(file_exists(FSHelper::fixOSFileName($path . $fname)));
        if (file_exists(FSHelper::fixOSFileName($path . $fname))) {
//          $debugStr = '+' . __LINE__ . '+';
          $doc = new Document();
          $fname = basename($fname);

          $doc->setTitle($this->propouseDocNameFromFile($fname));
          $doc->setPath($fname);
          if ($theme) {
            $doc->setTheme($theme);
//            $debugStr = '+' . __LINE__ . '+';
          }
          $doc->setTags('batch');

          $doc->setCreateDt(new \DateTime());
          $doc->setEditDt(new \DateTime());

          $dbm->persist($doc->getTheme());
          $dbm->persist($doc);
          $dbm->flush();

//          $debugStr = '+' . __LINE__ . '+';
          $cnt++;
        }
      }
    }
//          return new \Symfony\Component\HttpFoundation\Response("request: " . var_export($_SERVER, TRUE));
    //exit();
//          $theme = $dbm->getRepository("BigElibreBundle:Theme")->findOneByCode($themeCode);
//          return new \Symfony\Component\HttpFoundation\Response("theme: " . var_export($theme, TRUE));
//          var_dump($themeCode);


    return new Response('added ' . $cnt . ' documents');
  }

  private function propouseDocNameFromFile($fname) {
    $p = strrpos(basename($fname), '.');
    if ($p === FALSE) {
      $name = $fname;
    } else {
      $name = substr(basename($fname), 0, strrpos(basename($fname), '.'));
    }
    return $name;
  }

}
