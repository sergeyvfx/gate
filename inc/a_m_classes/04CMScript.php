<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Class for script tag
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

  if ($_CMScript_ != '#CMScript_Included#') {
    $_CMScript_ = '#CMScript_Included#';

    class CMScript extends CMHeadTag {
      var $innerHTML;

      function CMScript () { $this->SetClassName ('CMScript'); }

      function Init ($params, $innerHTML = '') {
        $this->SetDefaultSettings ();
        $this->closeTag = true;
        $this->innerHTML = $innerHTML;
        $this->SetClassName ('script');
        $this->SetSettings (unserialize_params ($params));
    }

      function SetDefaultSettings () { $this->SetClassName ('CMScript'); }
      function SetSource ($src) { $this->innerHTML = $src; }
      function InnerHTML () { return $this->innerHTML; }
  }

  content_Register_MCClass ('CMScript');
}
?>
