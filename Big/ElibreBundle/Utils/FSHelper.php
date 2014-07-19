<?php

namespace Big\ElibreBundle\Utils;

class FSHelper {

  static function fixOSFileName($fname, $forReading = FALSE) {
    if (($fname) && (strtolower(substr(PHP_OS, 0, 3)) === 'win')) {
      if ($forReading) {
        $fname = mb_convert_encoding($fname, "utf-8", "windows-1251");
      } else {
        $fname = mb_convert_encoding($fname, "windows-1251", "utf-8");
      }
    }
    return $fname;
  }
  
  static function getBaseName($fpath) {
    $a = explode(DIRECTORY_SEPARATOR, $fpath);
    return end($a);
  }

}
