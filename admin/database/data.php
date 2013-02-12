<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main handlers for settings administration
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
    redirect(config_get('document-root') . '/login');
  } else if (user_access_root ()) {
    header ('Location: table');
  } else {
    include '../inc/denied.php';
  }
?>
