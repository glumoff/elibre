<?php

namespace Big\ElibreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity
 */
class Page {

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
   * @ORM\Column(name="name", type="string", length=45)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="lang", type="string", length=5)
   */
  private $lang;

  /**
   * @var string
   *
   * @ORM\Column(name="title", type="string", length=100)
   */
  private $title;

  /**
   * @var string
   *
   * @ORM\Column(name="content", type="text")
   */
  private $content;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="createDT", type="datetime", name="create_dt")
   */
  private $createDT;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="editDT", type="datetime", name="edit_dt")
   */
  private $editDT;

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set name
   *
   * @param string $name
   * @return Page
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string 
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set lang
   *
   * @param string $lang
   * @return Page
   */
  public function setLang($lang) {
    $this->lang = $lang;

    return $this;
  }

  /**
   * Get lang
   *
   * @return string 
   */
  public function getLang() {
    return $this->lang;
  }

  /**
   * Set title
   *
   * @param string $title
   * @return Page
   */
  public function setTitle($title) {
    $this->title = $title;

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
   * Set content
   *
   * @param string $content
   * @return Page
   */
  public function setContent($content) {
    $this->content = $content;

    return $this;
  }

  /**
   * Get content
   *
   * @return string 
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * Set createDT
   *
   * @param \DateTime $createDT
   * @return Page
   */
  public function setCreateDT($createDT) {
    $this->createDT = $createDT;

    return $this;
  }

  /**
   * Get createDT
   *
   * @return \DateTime 
   */
  public function getCreateDT() {
    return $this->createDT;
  }

  /**
   * Set editDT
   *
   * @param \DateTime $editDT
   * @return Page
   */
  public function setEditDT($editDT) {
    $this->editDT = $editDT;

    return $this;
  }

  /**
   * Get editDT
   *
   * @return \DateTime 
   */
  public function getEditDT() {
    return $this->editDT;
  }

}