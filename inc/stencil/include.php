<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for frames
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

  if ($_stencil_Included_ != '#stencil_Included#') {
    $_stencil_Included_ = '#stencil_Included#';

    include $DOCUMENT_ROOT.'/inc/stencil/anchor.php';
    include $DOCUMENT_ROOT.'/inc/stencil/core_page.php';
    include $DOCUMENT_ROOT.'/inc/stencil/dd_form.php';
    include $DOCUMENT_ROOT.'/inc/stencil/form.php';
    include $DOCUMENT_ROOT.'/inc/stencil/frame.php';
    include $DOCUMENT_ROOT.'/inc/stencil/imaged_href.php';
    include $DOCUMENT_ROOT.'/inc/stencil/message.php';
    include $DOCUMENT_ROOT.'/inc/stencil/pages.php';
    include $DOCUMENT_ROOT.'/inc/stencil/tabcontrol.php';
    include $DOCUMENT_ROOT.'/inc/stencil/wiki_page.php';
    include $DOCUMENT_ROOT.'/inc/stencil/button.php';
    include $DOCUMENT_ROOT.'/inc/stencil/contents.php';
    include $DOCUMENT_ROOT.'/inc/stencil/pagintation.php';
    include $DOCUMENT_ROOT.'/inc/stencil/progress.php';

  }
?>
