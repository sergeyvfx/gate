<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Libraries' stuff
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

  if ($_WT_library_included_ != '##WT_library_Included##') {
    $_WT_library_included_ != '##WT_library_Included##';

    global $WT_library_container;
    $WT_library_container = nil;
    global $WT_libraries;

    class CGLibraryContainer {
      var $data;
      function CGLibraryContainer () { $this->data = array (); }

      function Register ($class, $pseudo, $lid) {
        $this->data[] = array ('class' => $class,
                               'pseudonym' => $pseudo,
                               'lid' => $lid);
      }

      function GetList () { return $this->data; }
    }

    function WT_spawn_new_library_container () {
      global $WT_library_container;

      if ($WT_library_container != nil) {
        return $WT_library_container;
      }

      $WT_library_container = new CGLibraryContainer ();
      return $WT_library_container;
    }

    function WT_spawn_new_library ($lid, $gw = nil) {
      global $WT_libraries;

      if (isset ($WT_libraries[$lid])) {
        return $WT_libraries[$lid];
      }

      if ($gw == nil) {
        $gw = WT_spawn_new_gateway ();
      }

      $cnt = WT_spawn_new_library_container ();
      $arr = $cnt->GetList ();
      $n = count ($arr);

      for ($i = 0; $i < $n; $i++) {
        if ($arr[$i]['lid'] == $lid) {
          $res = new $arr[$i]['class'] ($gw);
          $res->Init ();
          $WT_libraries[$lid] = $res;
          return $res;
        }
      }

      return nil;
    }

    function WT_library_register ($class, $pseudo, $lid) {
      $cnt = WT_spawn_new_library_container ();
      global $WT_library_container;
      $cnt = &$WT_library_container;
      $cnt->Register ($class, $pseudo, $lid);
    }

    function WT_library_selector ($name = '') {
      $cnt = WT_spawn_new_library_container ();
      $arr = $cnt->GetList ();
      $res = '<select name="'.$name.'_lib_selector" class="block">'."\n";

      $n = count ($arr);
      for ($i = 0; $i < $n; $i++) {
        $res .= '  <option value="'.$arr[$i]['lid'].'">'.
          $arr[$i]['pseudonym'].'</option>'."\n";
      }
      $res .= '</select>'."\n";

      return $res;
    }

    function WT_receive_library_from_selector ($name = '') {
      return $_POST[$name.'_lib_selector'];
    }
  }
?>
