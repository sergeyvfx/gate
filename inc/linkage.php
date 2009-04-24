<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main classes linkage
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

  if ($_linked_ != '#linked#') {
    $_linked_ = '#linked#';

    $dirs = array (
      '/inc/a_v_classes',
      '/inc/a_m_classes',
      '/inc/a_d_classes',
      '/inc/a_s_classes'
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

      array_multisort ($arr, SORT_ASC, SORT_STRING);
      foreach ($arr as $k) {
        include $DOCUMENT_ROOT.$k;
      }
      closedir ($dir);
    }
  }
?>
