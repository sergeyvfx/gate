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

  if ($stencil_frame_included != '#stencil_frame_Included#') {
    $stencil_frame_included = '#stencil_frame_Included#';

    function stencil_tframe ($content, $caption = '') {
      return tpl ('back/stencil/frame',
                  array ('content' => $content, 'caption' => $caption));
    }

    function stencil_groupo ($p = '')  {
      $p = unserialize_params ($p);
      return ('<div class="group"><span class="title">'.$p['title'].
              '</span><div class="content">');
    }

    function stencil_groupc () { return ('</div></div>'); }
  
    function stencil_lblocko ($p = array ()) {
      $p = unserialize_params ($p);

      if ($p['color'] != '') {
        $color = ' t'.$p['color'];
      }

      if ($p['bgcolor'] != '') {
        $bg = ' style="background-color: '.$p['bgcolor'].'"';
      }

      return '<div class="lblock"><div class="title'.$color.'">'.
        htmlspecialchars ($p['title']).'</div><div class="content"'.$bg.'>';
    }

    function stencil_lblockc () { return '</div></div>'; }

    function tframe ($content, $caption = '') {
      println (stencil_tframe ($content, $caption));
    }

    function groupo ($p = '') { println (stencil_groupo ($p)); }
    function groupc ()        { println (stencil_groupc ()); }

    function lblocko ($p = array ()) { println (stencil_lblocko ($p)); }
    function lblockc ()              { println (stencil_lblockc ()); }

  }
?>
