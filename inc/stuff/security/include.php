<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Includer of security stuff
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

  if ($_security_Included_ != '#security_Included#') {
    $_security_Included_ = '#security_Included#';

    include $DOCUMENT_ROOT.'/inc/stuff/security/group.php';
    include $DOCUMENT_ROOT.'/inc/stuff/security/security.php';
    include $DOCUMENT_ROOT.'/inc/stuff/security/user.php';
  }
?>
