<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for anchors
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

  if ($stencil_anchor_included != '#stencil_anchor_Included#') {
    $stencil_anchor_included = '#stencil_anchor_Included#';

    $anchor_appened = false;

    function stencil_dnd_anchor ($id = -1, $callback = '', $hint = '',
                                 $dragable = true) {
      global $anchor_appened, $CORE;
      $CORE->AddScriptFile ('anchor.js');
      add_body_handler ('onload', 'anchor_Register', array ("'$id'", $callback));

      if (!$anchor_appened) {
        add_body_handler ('onmousemove', 'anchor_OnMouseMove',  array ('event'));
        add_body_handler ('onscroll',    'anchor_OnPageScroll', array ('event'));
        add_body_handler ('onmouseup',   'anchor_StopDrag',     array ());
        $anchor_appened = true;
      }

      return tpl ('back/stencil/anchor', array ('id' => $id,
                                                'callback' => $callback,
                                                'hint' => $hint,
                                                'dragable' => $dragable));
    }

    function dnd_anchor ($id = -1, $callback = '', $hint = '',
                         $dragable = true) {
      println (stencil_dnd_anchor ($id, $callback, $hint, $dragable));
    }
  }
?>
