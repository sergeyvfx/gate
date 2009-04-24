<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Linkage of Wiki page classes
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

  if ($_wiki_linked_ != '#wiki_linked#') {
    $_wiki_linked_ = '#wiki_linked#';

    $dirs = array (
        '/inc/logick/wiki/a_c_classes'
      );

    foreach ($dirs as $path) {
      $dir = opendir ($DOCUMENT_ROOT.$path);
      $arr = array ();

      while (($file = readdir ($dir)) != false) {
        if ($file != '.' && $file != '..') {
          if (eregi (".*\.php$", $file)) {
            $arr[] = $path.'/'.$file;
          }
        }
      }

      array_multisort ($arr,SORT_ASC,SORT_STRING);
      foreach ($arr as $k) {
        include $DOCUMENT_ROOT.$k;
      }
    }
  }
?>
