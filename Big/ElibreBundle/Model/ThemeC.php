<?php

/**
 * Description of Theme
 *
 * @author glumoff
 */

namespace Big\ElibreBundle\Model;

class ThemeC {

  private $id;
  private $title;
  private $description;
  private $parentID;
  private $children;
  private $code;
  //private $subthemesCount;
  private $docsCount;

  public function getID() {
    return $this->id;
  }

  public function getCode() {
    return $this->code;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getDescription() {
    return $this->description;
  }

  public function getParentID() {
    return $this->parentID;
  }

  public function getChildren() {
    return $this->children;
  }

  public function getDocsCount() {
    return $this->docsCount;
  }

  public function setCode($code) {
    $this->code = $code;
  }

  public function setID($id) {
    $this->id = $id;
  }

  public function setTitle($title) {
    $this->title = $title;
  }

  public function setDescription($descr) {
    $this->description = $descr;
  }

  public function setChildren($children) {
    $this->children = $children;
  }
  
  public function setParentID($id) {
    $this->parentID = $id;
  }

  public function setDocsCount($cnt) {
    $this->docsCount = $cnt;
  }

  
}