<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for progress bar
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

  if ($_stencil_progress_Included_ != '#stencil_progress_Included#') {
    $_stencil_progress_Included_ = '#stencil_progress_Included#';

    function stencil_progress ($settings = '') {
      $s = unserialize_params ($settings);

      if ($s['width'] == '') {
        $s['width'] = 100;
      }

      $src = '<div class="progress" style="width: '.$s['width'].
        'px; height: 6px;">';

      if ($s['pos'] != '' && $s['pos'] != '0') {
        $src .= '<div class="progressStart"></div>';
        $src .= '<div class="progressBar" style="width: '.
          floor ($s['pos']/100*$s['width']-2).'px;"></div>';
        $src .= '<div class="progressEnd"></div>';
      } else {
        $src.='<img src="pics/site/clear.gif">';
      }
      $src .= '</div>';
      return $src;
    }

    function progress ($settings = '') {
      println (stencil_prorgess ($settings));
    }
  }
?>
