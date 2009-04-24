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

  if ($stencil_imagedHref_included != '#stencil_imagedHref_Included#') {
    $stencil_imagedHref_included = '#stencil_imagedHref_Included#';

    function stencil_imaged_href ($title, $href, $img) {
      return tpl ('back/stencil/imaged_href',
                  array ('title' => $title, 'href' => $href, 'img' => $img));
    }

    function stencil_ibtnav ($img, $nav, $title = '', $confirm = '') {
      $scr = (($confirm!='')?("if (cfrm ('$confirm'))"):(''))."nav ('$nav')";
      return ('<img class="'.(($nav!='')?('btn'):('btnd')).'" src="'.
              config_get ('document-root').'/pics/'.$img.'"'.(($nav!='')?
                                                (' onclick="'.$scr.'"'):('')).
              ' '.(($title!='')?('title="'.$title.'" alt="'.$title.
                                 '"'):('')).'>');
    }

    function stencil_titledimg ($img, $title) {
      return ('<img src="'.config_get ('document-root')."/pics/$img\"".
              (($title!='')?(" title=\"$title\""):('')).'>');
    }

    function stencil_imghelp ($title) { stencil_titledimg ('help.gif', $title); }

    function stencil_cbimage ($img, $onclick, $title = '') {
      return '<img src="'.config_get ('document-root').'/pics/'.$img.
        '" onclick="'.$onclick.'"'.(($title!='')?(' title="'.$title.
                              '"'):('')).' alt="'.$title.'" class="pointer">';
    }

    function imaged_href ($title, $href, $img) {
      println (stencil_imaged_href ($title, $href, $img));
    }

    function ibtnav ($img, $nav, $title = '', $confirm = '') {
      println (stencil_ibtnav ($img, $nav, $title, $confirm));
    }

    function titledimg ($img, $title) {
      println (stencil_titledimg ($img, $title));
    }

    function imghelp ($title) {
      println (stencil_imghelp ($title));
    }

    function cbimage ($img, $onclick, $title = '') {
      println (stencil_cbimage ($img, $onclick, $title));
    }
  }
?>
