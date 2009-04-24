<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Serialization of associative arrays
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $IFACE;

  if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
    print ('HACKERS?');
    die;
  }

  if ($_dbpack_included_ != '#dbpack_Included#') {
    $_cbpack_included_ = '#cbpack_Included#';

    // Unpacks packed assaciative array
    function db_unpack ($self) {
      $result = array ();
      $token  = '';
      $act    = 0; // 0 - reading parameter name
                   // 1 - reading parameter's value length
                   // 2 - reading oarameter's value

      $i = 0;
      while ($i < strlen ($self)) {
        if ($self[$i]==';') {
          if ($act == 0) {
            $act = 1;
            $key = $token;
            $token = '';
          } else if ($act == 1) {
            $act = 2;
            $len = $token;
            $token = '';
            $i++;

            for ($j = 0; $j < $len; $j++) {
              $token.=$self[$i];  $i++;
            }

            $value = $token;
            $token = '';
            $i--;
          } else if ($act == 2) {
            $result[$key] = $value;
            $act = 0;
          }
        } else {
          $token.=$self[$i];
        }

        $i++;
      }

      return $result;
    }

    // Packs associative array 2 string
    function db_pack ($self) {
      $result = '';
      foreach ($self as $key => $value) {
        $result .= $key.';';
        $result .= strlen ($value).';';
        $result .= $value.';';
      }
      return $result;
    }
  }
?>
