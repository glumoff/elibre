<?php

/**
 * Description of ThemesList
 *
 * @author glumoff
 */

namespace Big\ElibreBundle\Model;

class ThemesTree {

  private $themes_arr;
  private $maxDepthLevel = NULL;

  public function __construct($arr = NULL) {
    if (isset($arr))
//      $this->buildFromArray($arr);
      $this->themes_arr = $arr;
  }

  //TODO: make navigation with iterator    
  public function getThemesArray() {
    return $this->themes_arr;
  }

  public function setMaxLevel($level) {
    $this->maxDepthLevel = $level;
  }

  /**
   * 
   * @param ThemeC $theme
   */
  public function addTheme($theme) {
    $this->themes_arr[$theme->getID()] = $theme;
  }

  public function buildFromArray($arr) {
    $this->themes_arr = $this->parseArray($arr);
//    echo "----------------------<br>";
  }

  private function parseArray($arr, $root = 0, $level = NULL) {
//    echo 'ROOT1: ' . $root . '<br>';
    $res = NULL;
    if (($this->maxDepthLevel !== NULL) && ($level >= $this->maxDepthLevel)) {
      return $res;
    }
//    echo 'ROOT2: ' . $root . '<br>';
    if (is_array($arr)) {
//    echo 'ROOT3: ' . $arr . '<br>';
      foreach ($arr as $k => $v) {
        $t = new ThemeC();
        $t->setID($v['id']);
        $t->setCode($v['code']);
        $t->setTitle($v['title']);
        $t->setDescription($v['descr']);
        $t->setParentID($v['parent_id']);
        $t->setDocsCount($v['docsCount']);
        if ($v['parent_id'] == $root) {
          unset($arr[$k]);
//          echo $k . ': ' . $v['title'] . "; parent: " . var_export($v['parent_id'], TRUE) . '<br>';
          $res[] = $t;
//          $tl = new ThemesTree();
          $res[count($res) - 1]->setChildren(new ThemesTree($this->parseArray(&$arr, $v['id'], $level + 1)));
        }
      }
    }
    return $res;
  }

  /**
   * 
   * @param string $code
   * @return ThemeC
   */
  public function getTheme($code) {
    $res = NULL;
//    echo '<pre>' . var_export($this->themes_arr, true) . '</pre>';
    if (is_array($this->themes_arr)) {
      /** Theme $t */
      foreach ($this->themes_arr as $t) {
        if ($res)
          break;
        if ($t->getCode() == $code) {
          return $t;
        }
        elseif ($t->getChildren()) {
          $res = $t->getChildren()->getTheme($code);
        }
      }
    }
    return $res;
  }

  /**
   * 
   * @param int $id
   * @return ThemeC
   */
  public function getThemeByID($id) {
    $res = NULL;
    if (is_array($this->themes_arr)) {
      /** Theme $t */
      foreach ($this->themes_arr as $t) {
        if ($res)
          break;
        if ($t->getID() == $id) {
          return $t;
        }
        elseif ($t->getChildren()) {
          $res = $t->getChildren()->getThemeByID($id);
        }
      }
      return $res;
    }
  }

  /**
   * 
   * @param string $code
   * @return ThemeC
   */
  public function getThemePath($code) {
    $pathArr = array();
    $t = $this->getTheme($code);
    if ($t) {
      if (is_array($this->themes_arr)) {
        do {
          /** Theme $parent */
          $parent = $this->getThemeByID($t->getParentID());
          if ($parent) {
            $t = $parent;
            $pathArr[] = $t;
          }
        } while ($parent !== NULL);
      }
    }
    return $pathArr;
  }

}

