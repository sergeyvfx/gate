<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil main stuff
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

  if ($_stencil_included_ != '#stencil_Included#') {
    $_stencil_included_='#stencil_Included#'; 

    function stencil_set ($k, $v) {
      global $stencil_vars;
      $stencil_vars[$k] = $v;
    }

    function stencil_get ($k) {
      global $stencil_vars;
      return $stencil_vars[$k];
    }

    function stencil_render () {
      $body = stencil_get ('boby');
      if ($body) {
        $stencil_body = eval_code ($body);
      } else {
        $stencil_body = stencil_get ('stencil_body');
      }
      tplp ('stencil/'.stencil_get ('stencil_template'), array ('body'=>$stencil_body));
    }
  }
?>
