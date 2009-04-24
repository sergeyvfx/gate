<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Includer of Wiki stuff
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

  if ($_wiki_Included_ != '#wiki_Included#') {
    $_wiki_Included_ = '#wiki_Included#';
    include $DOCUMENT_ROOT.'/inc/logick/wiki/linkage.php';
    include $DOCUMENT_ROOT.'/inc/logick/wiki/content.php';
    include $DOCUMENT_ROOT.'/inc/logick/wiki/wiki.php';
    include $DOCUMENT_ROOT.'/inc/logick/wiki/menu.php';
  }
?>
