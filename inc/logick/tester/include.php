<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Includer of WebTester's Web-interface
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

  if ($_WT_included_ != '###WT_included###') {
    $_WT_included_ != '###WT_included###';

    include $DOCUMENT_ROOT.'/inc/include.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/config/config.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/config/compilers.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/config/errors.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/config/ipc.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/config/wt.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/library.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/contest.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/security.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/linkage.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/compilers.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/gateway.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/ipc.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/util.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/wt.php';
    include $DOCUMENT_ROOT.'/inc/logick/tester/data_receiver.php';
  }
?>
