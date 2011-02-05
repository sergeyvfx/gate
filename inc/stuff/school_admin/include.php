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

  if ($_school_Included_ != '#school_Included#') {
    $_school_Included_ = '#school_Included#';

    include $DOCUMENT_ROOT.'/inc/stuff/school_admin/school.php';
    include $DOCUMENT_ROOT.'/inc/stuff/school_admin/school_admin.php';
  }
?>
