<?php

namespace Big\ElibreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * History
 *
 * @ORM\Table(name="history")
 * @ORM\Entity
 */
class History {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var integer
   *
   * @ORM\Column(name="row_id", type="integer", unique=true)
   */
  private $row_id; // for table rotating

  /**
   * @var datetime
   *
   * @ORM\Column(name="dt", type="datetime")
   */
  private $dt;

  /**
   * @ORM\ManyToOne(targetEntity="Document")
   * @ORM\JoinColumn(name="doc_id", referencedColumnName="id")
   */
  private $document;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   */
  private $user;

  /**
   * @ORM\Column(name="action", type="string")
   */
  private $action;

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /*
   * Set document
   *
   * @param Document $document
   * @return History
   */

  public function setDocument(Document $document) {
    $this->document = $document;
    return $this;
  }

  /**
   * Get document
   *
   * @return Document
   */
  public function getDocument() {
    return $this->document;
  }

  /*
   * Set user
   *
   * @param User $user
   * @return History
   */

  public function setUser(User $user) {
    $this->user = $user;
    return $this;
  }

  /**
   * Get user
   *
   * @return User
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Set dt
   *
   * @param \DateTime $dt
   * @return History
   */
  public function setDt(\DateTime $dt = NULL) {
    if ($dt) {
      $dt = new \DateTime();
    }
    $this->dt = $dt;
    return $this;
  }

  /**
   * Get dt
   *
   * @return \DateTime 
   */
  public function getDt() {
    return $this->dt;
  }


    /**
     * Set action
     *
     * @param string $action
     * @return History
     */
    public function setAction($action)
    {
        $this->action = $action;
    
        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }
}