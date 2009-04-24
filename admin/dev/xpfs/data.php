<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main handlers for XPFS browser
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */


  if ($PHP_SELF != '') {
    print 'HACKERS?';
    die;
  }

  if (!user_authorized () || !user_access_root ()) {
    header ('Location: '.config_get ('document-root').'/admin');
  }

  global $DOCUMENT_ROOT;
  include $DOCUMENT_ROOT.'/inc/xpfs_browser.php';
  include $DOCUMENT_ROOT.'/admin/inc/menu.php';
  include '../menu.php';

  $manage_menu->SetActive ('to-developer');
  $mandev_menu->SetActive ('xpfs');

  // Printing da page
  print ($manage_menu->InnerHTML ());
  print ($mandev_menu->InnerHTML ());
  print ('${information}');

  $browser = new XPFSBrowser ();
  $browser->interact ();
  $browser->Draw ();
?>
