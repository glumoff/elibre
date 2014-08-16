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
  }

  private function rotateHistory() {
    $query = $this->dbm->createQuery(
                    'REPLACE INTO history
                     SET row_id = (SELECT COALESCE(MAX(log_id), 0) % "" + 1
              FROM circular_log_table AS t),
    payload = ""');
//                      SELECT h
//                      FROM BigElibreBundle:History h
//                      ORDER BY h.dt DESC'
//            );
//            )->setMaxResults(10);
//WHERE create_dt + INTERVAL 30 DAY > CURRENT_DATE
    $query->execute();
  }
  
}
