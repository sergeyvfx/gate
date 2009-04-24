<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Class for link tag
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

  if ($_CMLink_ != '#CMLink_Included#') {
    $_CMLink_ = '#CMLink_Included#';

    class CMLink extends CMHeadTag {
      function CMLink () { $this->SetClassName ('CMLink'); }

      function Init ($params) {
        $this->SetDefaultSettings ();
        $this->SetClassName ('link');
        $this->SetSettings (unserialize_params ($params));
      }

      function SetDefaultSettings () { $this->SetClassName ('CMLink'); }
    }

    content_Register_MCClass ('CMLink');
  }
?>
