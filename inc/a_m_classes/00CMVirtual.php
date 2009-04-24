<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Virtual class for meta classes
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

  if ($_CMVirtual_ != '#CMVirtual_Included#') {
    $_CMVirtual_ = '#CMVirtual_Included#';

    class CMVirtual extends CVCVirtual {
      function CMVirtual () { $this->SetClassName ('CMVirtual'); }
      function SetDefaultSettings () { $this->SetClassName ('CMVirtual'); }
    }

    content_Register_MCClass ('CMVirtual');
  }
?>
