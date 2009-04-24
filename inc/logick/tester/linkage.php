<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Linkage of Web-Interface classes
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

  if ($_WT_linked_ != '#linked#') {
    $_WT_linked_ = '#linked#';

    $dirs = array (
        '/inc/logick/tester/modules'
      );

    linkage ($dirs);
  }
?>
