<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for wiki page
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

  if ($_stencil_wikiPage__Included_ != '#stencil_wikiPage_Included#') {
    $_stencil_wikiPage_Included_ = '#stencil_wikiPage_Included#';

    function stencil_wiki_page ($content, $tabs) {
      return tpl ('back/stencil/wiki_page',
                  array ('content' => $content, 'tabs' => $tabs));
    }

    function wiki_page ($content, $tabs) {
      println (stencil_wiki_page ($content, $tabs));
    }
  }
?>
