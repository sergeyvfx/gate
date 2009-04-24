<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for different pages
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

  if ($_stencil_pages_Included_ != '#stencil_pages_Included#') {
    $_stencil_pages_Included_ = '#stencil_pages_Included#';

    function stencil_on_construction () {
      return tpl ('back/on_construction');
    }

    function on_construction () {
      println (stencil_on_construction ());
    }
  }
?>
