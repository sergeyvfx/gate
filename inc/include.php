<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Includes of main modules
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

  if ($_all_included_ != '#all_Included#') {
    $_all_included_ = '#all_Included#'; 

    include $DOCUMENT_ROOT.'/inc/stuff/parsers.php';
    include $DOCUMENT_ROOT.'/inc/config.php';
    include $DOCUMENT_ROOT.'/inc/common/config.php';
    include $DOCUMENT_ROOT.'/inc/content.php';
    include $DOCUMENT_ROOT.'/inc/common/CVirtual.php';
    include $DOCUMENT_ROOT.'/inc/service.php';
    include $DOCUMENT_ROOT.'/inc/logick/wiki/include.php';
    include $DOCUMENT_ROOT.'/inc/stuff/include.php';
    include $DOCUMENT_ROOT.'/inc/stuff/security/include.php';
    include $DOCUMENT_ROOT.'/inc/stencil/include.php';
    include $DOCUMENT_ROOT.'/inc/linkage.php';
    include $DOCUMENT_ROOT.'/inc/settings.php';
    include $DOCUMENT_ROOT.'/inc/dev.php';
    include $DOCUMENT_ROOT.'/inc/builtin.php';
    include $DOCUMENT_ROOT.'/inc/template.php'; 
    include $DOCUMENT_ROOT.'/inc/stencil.php'; 
    include $DOCUMENT_ROOT.'/inc/xpfs.php';
    include $DOCUMENT_ROOT.'/inc/main.php';
  }
?>
