<?php

/**
 * Description of ElibreDBDelegate
 *
 * @author glumoff
 */

namespace Big\ElibreBundle\db;

use Big\ElibreBundle\db\DBDelegateA;
use Big\ElibreBundle\Model\ThemesTree;
use Big\ElibreBundle\Model\ThemeC;
use Big\ElibreBundle\Entity\Document;
use Big\ElibreBundle\Model\DocumentsList;

class ElibreDBDelegate extends DBDelegateA {

  public function getConn() {
    return new ElibreDBConnection();
  }

  public function getThemes($level = NULL, $role = NULL) {
    $res = false;
    $accessLevel = '';
    if ($role) {
      if (($role == 'ROLE_USER') || ($role == 'IS_AUTHENTICATED_ANONYMOUSLY')) {
        $accessLevel = ' AND is_public=1';
      }
    }
    $query = 'SELECT  *,
                      (SELECT COUNT(*) 
                       FROM documents
                       WHERE documents.theme_id = themes.id) AS docsCount
              FROM themes
              WHERE is_active=1'
            . $accessLevel
            . ' ORDER BY parent_id, show_order, id';
    $dbm = $this->getDBM();
//    echo $role;
    $res = $dbm->selectQuery($query);
    $tl = new ThemesTree();
    $tl->setMaxLevel($level);
    $tl->buildFromArray($res);

    return $tl;
  }

  public function getSubThemes($themeCode, $role = NULL) {
    //$res = false;
    $accessLevel = '';
    if ($role) {
      if (($role == 'ROLE_USER') || ($role == 'IS_AUTHENTICATED_ANONYMOUSLY')) {
        $accessLevel = ' AND t2.is_public=1';
      }
    }
    $query = "SELECT t2.*
              FROM themes t1
              INNER JOIN themes t2
                      ON t2.parent_id = t1.id
              WHERE t1.code = '" . $themeCode . "'
                 AND t2.is_active=1"
            . $accessLevel
            . " ORDER BY title, code";
//    echo $query;
    $dbm = $this->getDBM();
    $res = $dbm->selectQuery($query, FALSE);
    $tl = new ThemesTree();
    if ($res && (is_array($res))) {
      foreach ($res as $v) {
        $t = new ThemeC();
        $t->setID($v['id']);
        $t->setCode($v['code']);
        $t->setTitle($v['title']);
        $t->setDescription($v['descr']);
//        $t->setParent($v['parent_id']);
        $tl->addTheme($t);
      }
    }
    return $tl;
  }

  public function getTheme($themeID) {
    $t = NULL;
    $query = "SELECT *
              FROM themes
              WHERE id = '" . $themeID . "'
              ORDER BY title, code";
    $dbm = $this->getDBM();
    $res = $dbm->selectQuery($query);
    if ($res) {
      $t = new ThemeC();
      $t->setID($res['id']);
      $t->setCode($res['code']);
      $t->setTitle($res['title']);
      $t->setDescription($res['descr']);
    }
    return $t;
  }

  public function getThemeByCode($themeCode, $role = NULL) {
    $t = NULL;
    $accessLevel = '';
    if ($role) {
      if (($role == 'ROLE_USER') || ($role == 'IS_AUTHENTICATED_ANONYMOUSLY')) {
        $accessLevel = ' AND is_public=1';
      }
    }
    $query = "SELECT *
              FROM themes
              WHERE code = '" . $themeCode . "'"
            . $accessLevel
            . " ORDER BY title, code";
//    echo $query;
//    exit;
    $dbm = $this->getDBM();
    $res = $dbm->selectQuery($query);
    if ($res) {
      $t = new ThemeC();
      $t->setID($res['id']);
      $t->setCode($res['code']);
      $t->setTitle($res['title']);
      $t->setDescription($res['descr']);
//        $t->setParent($v['parent_id']);
    }
    return $t;
  }

  public function getDocuments($themeID, $role = NULL) {
    $res = false;
    $accessLevel = '';
    if ($role) {
      if (($role == 'ROLE_USER') || ($role == 'IS_AUTHENTICATED_ANONYMOUSLY')) {
        $accessLevel = ' AND themes.is_public=1';
      }
    }
    $query = 'SELECT documents.* '
            . 'FROM documents, themes '
            . 'WHERE themes.id=documents.theme_id '
            . 'AND theme_id=' . $themeID . ' '
            . $accessLevel
            . ' ORDER BY title, id;';
    $dbm = $this->getDBM();
    $res = $dbm->selectQuery($query, FALSE);

    $dl = new DocumentsList();
    if ($res && (is_array($res))) {
      foreach ($res as $v) {
        $d = new Document($v);
        $dl->addDoc($d);
      }
    }
    return $dl;
  }

  /**
   * 
   * @param int $docID
   * @return \Big\ElibreBundle\Model\Document
   */
  public function getDocument($docID, $role = NULL) {
    $d = NULL;
    $query = 'SELECT * 
              FROM documents
              WHERE id=' . $docID . '
              ORDER BY title, id';
    $dbm = $this->getDBM();
    $res = $dbm->selectQuery($query, FALSE);

    if ($res && (is_array($res))) {
      $d = new Document($res[0]);
    }
    return $d;
  }

  public function savePageContent($page, $content) {
//    $query = 'UPDATE ';
    $dbm = $this->getDBM();
    $res = $dbm->executeQuery($query);
  }

  /**
   * 
   * @param integer $theme_id
   */
  private function getThemeFullDirName($theme_id, $dbm = NULL) {
//    $dbm = $thi
//    s->getDoctrine()->getManager();
    $path = '';

    do {
      /* @var $theme Theme */
      $theme = $dbm->getRepository("BigElibreBundle:Theme")->find($theme_id);
      if (!$theme) {
        break;
      }
      $path = '/' . $theme->getDirName() . $path;
      $theme_id = $theme->getParentId();
    } while ($theme_id > 0);

    return $path;
  }

}

class ElibreDBConnection {

  var $host = 'localhost';
  var $user = 'elibre';
  var $pwd = 'KorgIget';
  var $dataBase = 'elibre';

}
