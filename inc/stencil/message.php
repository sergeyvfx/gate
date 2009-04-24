<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for messages
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

  if ($_stencil_messages_Included_ != '#stencil_messages_Included#') {
    $_stencil_messages_Included_ = '#stencil_messages_Included#';

    function stencil_info ($txt) {
      if ($txt != '') {
        return '<div class="info"><table width="100%"><tr>'.
          '<td rowspan="2" class="img"><img src="'.
          config_get ('document-root').'/pics/info24.gif"></td>'.
          '<td class="title">Информация</td></tr><tr><td class="msg">'.
          $txt.'</td></tr></table></div>';
      } else {
        return '';
      }
    }

    function info ($txt) {
      println (stencil_info ($txt));
    }
  }
?>
