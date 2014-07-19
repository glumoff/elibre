<?php

namespace Big\ElibreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * Description of User
 *
 * @author Alexander Glumoff <glumoff at gmail.com>
 */

/**
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User extends BaseUser {

  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  public function __construct() {
    parent::__construct();
//    $this->locked = false;
//    $this->expired = false;
//    $this->credentialsExpired = false;
//    $this->salt = md5(uniqid(null, true));
  }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}