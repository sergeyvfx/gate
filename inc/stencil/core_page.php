<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for core (non-wiki) page
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

  if ($stencil_corePage_included != '#stencil_corePage_Included#') {
    $stencil_corePage_included = '#stencil_corePage_Included#';

    function stencil_core_page ($content, $caption = 'Системная страница') {
      return tpl ('back/stencil/core_page',
                  array ('content' => $content, 'caption' => $caption));
    }

    function core_page ($content, $caption = 'Системная страница') {
      println (stencil_core_page ($content, $caption));
    }
  }
?>
