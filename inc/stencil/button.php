<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for different buttons
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

  if ($stencil_button_included != '#stencil_button_Included#') {
    $stencil_button_included = '#stencil_button_Included#';

    function stencil_block_button ($caption, $onclick = 'dn();',
                                   $title = '', $float = false) {
      $title = trim ($title);
      return '<div class="blockButton'.(($float)?(' btnFloat'):('')).'"'.
        (($title!='')?(' title="'.htmlspecialchars ($title).'"'):('')).
        '><a href="JavaScript:'.$onclick.'">'.$caption.'</a></div>';
    }
  
    function stencil_block_img_button ($img,  $onclick = 'dn();',
                                       $title = '', $float = false) {
      return stencil_block_button ('<img src="'.config_get ('document-root').
                                   '/pics/'.$img.'"'.
                                   ((trim ($title)!='')?(' alt="'.
                                       htmlspecialchars ($title).'"'):('')).
                                   '>', $onclick, $title, $float);
    }

    function stencil_button ($caption, $onclick = 'dn();', $title = '') {
      return stencil_block_button ($caption, $onclick, $title, true);
    }

    function stencil_img_button ($img, $onclick = 'dn();', $title = '') {
      return stencil_block_img_button ($img, $onclick, $title, true);
    }

    function stencil_button_separator () {
      return '<div class="btnSeparator"><img src="'.
        config_get ('document-root').'/pics/clear.gif"></div>';
    }

    function stencil_updownbtn ($num, $count, $id = -1, $url = '.',
                                $idcode = 'id', $actcode = 'action',
                                $suffix = '') {
      $res = '';
      $s = (($suffix!='')?((($suffix[0]=='#')?(''):('&')).$suffix):(''));
      
      if ($url[strlen ($url)-1]=='.' ||
          $url[strlen ($url)-1]=='/') {
        $url.='?';
      } else {
        $url.='&';
      }

      if ($num < $count - 1) {
        $res.=stencil_ibtnav ('arrdown_blue.gif', $url.$actcode.'=down&'.
                              $idcode.'='.$id.$s, 'Опустить');
      } else {
        $res.=stencil_ibtnav ('arrdown_d.gif', '', 'Опустить');
      }

      if ($num > 0) {
        $res.=stencil_ibtnav ('arrup_blue.gif', $url.$actcode.'=up&'.
                              $idcode.'='.$id.$s, 'Поднять');
      } else {
        $res.=stencil_ibtnav ('arrup_d.gif', '', 'Поднять');
      }

      return $res;
    }

    function block_button ($caption, $onclick = 'dn();', $title = '') {
      println (stencil_block_button ($caption, $onclick, $title));
    }

    function block_img_button ($img, $onclick = 'dn();', $title = '') {
      println (stencil_block_img_button ($img, $onclick, $title));
    }

    function button ($caption, $onclick = 'dn();', $title = '') {
      println (stencil_button ($caption, $onclick, $title));
    }

    function img_button ($img, $onclick = 'dn();', $title = '') {
      println (stencil_img_button ($img, $onclick, $title));
    }

    function button_separator () { println (stencil_button_separator ()); }

    function updownbtn ($num, $count, $id = -1,$url = '.', $idcode = 'id',
                        $actcode = 'action', $suffix = '') {
      println (stencil_updownbtn ($num, $count, $id, $url,
                                  $idcode, $actcode, $suffix));
    }
  }
?>
