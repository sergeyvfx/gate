<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Class for meta
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

  if ($_CMMeta_ != '#CMMeta_Included#') {
    $_CMMeta_ = '#CMMeta_Included#';

    class CMMeta extends CMHeadTag {

      function CMMeta () { $this->SetClassName ('CMMeta'); }

      function Init ($params) {
        $this->SetDefaultSettings ();
        $this->SetClassName ('meta');
        $this->SetSettings (unserialize_params ($params));
      }

      function SetDefaultSettings () {$this->SetClassName ('CMMeta');}
    }

    content_Register_MCClass ('CMMeta');
  }
?>
