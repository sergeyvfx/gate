<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stuff modules includer
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

  if ($_stuff_Included_ != '#stuff_Included#') {
    $_stuff_Included_ = '#stuff_Included#';

    include $DOCUMENT_ROOT.'/inc/stuff/dbase.php';
    include $DOCUMENT_ROOT.'/inc/stuff/debug.php';
    include $DOCUMENT_ROOT.'/inc/stuff/hook.php';
    include $DOCUMENT_ROOT.'/inc/stuff/file.php';
    include $DOCUMENT_ROOT.'/inc/stuff/mail.php';
    include $DOCUMENT_ROOT.'/inc/stuff/ipc.php';
    include $DOCUMENT_ROOT.'/inc/stuff/linkage.php';
    include $DOCUMENT_ROOT.'/inc/stuff/redirect.php';
    include $DOCUMENT_ROOT.'/inc/stuff/editor.php';
    include $DOCUMENT_ROOT.'/inc/stuff/log.php';
    include $DOCUMENT_ROOT.'/inc/stuff/iframe/iframe.php';
    include $DOCUMENT_ROOT.'/inc/stuff/calendar.php';
    include $DOCUMENT_ROOT.'/inc/stuff/image/image_validator.php';
    include $DOCUMENT_ROOT.'/inc/stuff/handler.php';
    include $DOCUMENT_ROOT.'/inc/stuff/db_pack.php';
    include $DOCUMENT_ROOT.'/inc/stuff/sock.php';
  }
?>
