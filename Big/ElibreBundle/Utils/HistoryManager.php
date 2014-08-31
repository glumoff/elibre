<?php

/**
 * Description of HistoryManager
 *
 * @author panikovsky
 */

namespace Big\ElibreBundle\Utils;

use Big\ElibreBundle\Entity\History;
use Big\ElibreBundle\Entity\User;
use Big\ElibreBundle\Entity\Document;

class HistoryManager {

  private $dbm;
  private $rotateCount;

//  public function __construct($dbm) {
//    $this->dbm = $dbm;
//  }

  public function setDbm($dbm) {
    $this->dbm = $dbm;
  }

  public function setRotateCount($cnt) {
    $this->rotateCount = $cnt;
  }

  public function saveHistory(User $user, Document $doc, $action = 'download', DateTime $dt = NULL) {
    $h = new History();
    $h->setUser($user);
    $h->setDocument($doc);
    $h->setDt($dt ? $dt : new \DateTime());
    $h->setAction($action);
    $this->dbm->persist($h);
    $this->dbm->flush();
    $this->rotateHistory($user);
  }

  private function rotateHistory($user) {
    if (!$user) { // no user no work
      return;
    }

    $getQuery = $this->dbm->createQuery(
//                   'DELETE FROM history
//                      WHERE dt < (
//                        SELECT dt FROM history
//                        ORDER BY dt desc
//                        LIMIT 1 OFFSET ' + $this->rotateCount - 1 + '
//                      )
//');
                    'SELECT h
                      FROM BigElibreBundle:History h
                      WHERE h.user = ?1
                      ORDER BY h.dt DESC')
            ->setFirstResult($this->rotateCount - 1)
            ->setMaxResults(1)
            ->setParameter(1, $user);
    $limitHistory = $getQuery->getResult();
    if ($limitHistory && $limitHistory[0] && $limitHistory[0]->getDt()) {
      $delQuery = $this->dbm->createQuery(
                      'DELETE FROM BigElibreBundle:History h
              WHERE (h.dt < ?1)
                AND (h.user = ?2)'
              )
              ->setParameter(1, $limitHistory[0]->getDt())
              ->setParameter(2, $user);
      $delQuery->execute();
    }
  }

}
