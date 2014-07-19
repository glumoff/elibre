<?php

namespace Big\ElibreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Theme
 *
 * @ORM\Table(name="themes")
 * @ORM\Entity
 */
class Theme {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="title", type="string", length=30)
   */
  private $title;

  /**
   * @var string
   *
   * @ORM\Column(name="descr", type="string", length=100, nullable=false)
   */
  private $descr;

  /**
   * @var integer
   *
   * @ORM\Column(name="parent_id", type="integer")
   */
  private $parentId;

  /**
   * @var string
   *
   * @ORM\Column(name="code", type="string", length=45)
   */
  private $code;

  /**
   * @var boolean
   *
   * @ORM\Column(name="is_active", type="boolean")
   */
  private $isActive;

  /**
   * @var integer
   *
   * @ORM\Column(name="show_order", type="integer")
   */
  private $showOrder;

  /**
   * @ORM\Column(name="dir_name", type="string", length=255)
   */
  private $dirName;

  public function __construct() {
    $this->parentId = 0;
    $this->isActive = FALSE;
    $this->showOrder = -1;
  }

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id
   *
   * @param integer $id
   * @return Theme
   */
  public function setId($id) {
    $this->id = $id;

    return $this;
  }

  /**
   * Set title
   *
   * @param string $title
   * @return Theme
   */
  public function setTitle($title) {
    $this->title = $title;
    $this->setDirName($title);

    return $this;
  }

  /**
   * Get title
   *
   * @return string 
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Set descr
   *
   * @param string $descr
   * @return Theme
   */
  public function setDescr($descr) {
    $this->descr = $descr;

    return $this;
  }

  /**
   * Get descr
   *
   * @return string 
   */
  public function getDescr() {
    return $this->descr;
  }

  /**
   * Set parentId
   *
   * @param integer $parentId
   * @return Theme
   */
  public function setParentId($parentId) {
    $this->parentId = $parentId;

    return $this;
  }

  /**
   * Get parentId
   *
   * @return integer 
   */
  public function getParentId() {
    return $this->parentId;
  }

  /**
   * Set code
   *
   * @param string $code
   * @return Theme
   */
  public function setCode($code) {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return string 
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * Set visibility
   *
   * @param boolean $isActive
   * @return Theme
   */
  public function setIsActive($isActive) {
    $this->isActive = $isActive;

    return $this;
  }

  /**
   * Get visibility
   *
   * @return boolean 
   */
  public function isActive() {
    return $this->isActive;
  }

  /**
   * Set showOrder
   *
   * @param integer $showOrder
   * @return Theme
   */
  public function setShowOrder($showOrder) {
    $this->showOrder = $showOrder;

    return $this;
  }

  /**
   * Get showOrder
   *
   * @return integer 
   */
  public function getShowOrder() {
    return $this->showOrder;
  }

  /**
   * Set dirName
   *
   * @param string $dirName
   * @return Theme
   */
  public function setDirName($dirName) {
    // sanitizing dirName
    $dirName = preg_replace('/\s/','_', $dirName);
    $dirName = preg_replace('/[^\w-\.,]/ui','', $dirName);
            
    $this->dirName = $dirName;

    return $this;
  }

  /**
   * Get dirName
   *
   * @return string 
   */
  public function getDirName() {
    if (!$this->dirName) {
      $this->setDirName($this->getTitle());
    }
    return $this->dirName;
  }

  /**
   * Get getFullDirName
   *
   * @return string 
   */
  public function getFullDirName() {
    return $this->dirName;
  }
  

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}