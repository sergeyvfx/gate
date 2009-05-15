<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for drop-down form
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

  if ($stencil_ddForm_included != '#stencil_ddForm_Included#') {
    $stencil_ddForm_included = '#stencil_ddForm_Included#';

    function stencil_dd_formo ($settings = '') {
      global $dd_form_stuff_included, $CORE;

      $CORE->AddScriptFile ('dd_form.js');
      $s = unserialize_params ($settings);

      $onexpand = $s['onexpand'];
      if ($onexpand != '') {
        $onexpand = ' '.$onexpand;
      }

      $onhide = $s['onhide'];
      if ($onhide != '') {
        $onhide = ' '.$onhide;
      }

      $expanded = $s['expanded'];

      return ('<div class="dd_form"><div id="title" class="dd_title">'.
              '<table><tr><td><img src="'.config_get ('document-root').
              '/pics/arrdown_green.gif" id="show" '.
              'onclick="dd_form_expand (this);'.$onexpand.
              '" alt="Развернуть" title="Развернуть" '.
                ($expanded ? ' style="display: none;"' : '') .'>'.
              '<img src="'.config_get ('document-root').
              '/pics/arrup_red.gif" '.
              ( $expanded ? '' : 'style="display: none;"').' id="hide" '.
              'onclick="dd_form_hide (this);'.$onhide.'" alt="Свернуть" '.
              'title="Свернуть"></td><td>'.$s['title'].'</td></tr>'.
              '</table></div><div class="dd_content" id="content"'.
              (($expanded) ? (' style="display: block;"') : ('')).'>');
    }

    function stencil_dd_formc () {
      return ('</div></div>');
    }

    function dd_formo ($settings = '') {
      println (stencil_dd_formo ($settings));
    }

    function dd_formc () {
      println (stencil_dd_formc ());
    }
  }
?>
