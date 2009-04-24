<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Content generator for administration page
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF!='') {
    print 'HACKERS?';
    die;
  }

  if (!user_authorized ()) {
    include 'inc/login.php';
  } else if (user_access_root ()) {
    header ('Location: content');
  } else {
    include 'inc/denied.php';
  }
?>
