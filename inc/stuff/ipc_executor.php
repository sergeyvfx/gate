<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Helper for executor of IPC commands
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

  if ($_ipc_executor_included_ != '#ipc_executor_Included#') {
    $_ipc_executor_included_ = '#ipc_executor_Included#';

    global $ipc, $XPFS;

    /* Execute IPC command withot including all stuff  */

    /* Include required stuff */
    include $DOCUMENT_ROOT.'/inc/stuff/parsers.php';
    include $DOCUMENT_ROOT.'/inc/stuff/linkage.php';
    include $DOCUMENT_ROOT.'/inc/config.php';
    include $DOCUMENT_ROOT.'/inc/common/config.php';
    include $DOCUMENT_ROOT.'/inc/stuff/dbase.php';
    include $DOCUMENT_ROOT.'/inc/builtin.php';
    include $DOCUMENT_ROOT.'/inc/xpfs.php';
    include $DOCUMENT_ROOT.'/inc/stuff/security/user.php';
    include $DOCUMENT_ROOT.'/inc/stuff/ipc.php';

    db_connect (false);

    $XPFS = new XPFS ();
    $XPFS->createVolume ();
  }
?>
