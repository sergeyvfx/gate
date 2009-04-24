<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Virtual class for visual classes
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

  if ($_CVCVirtual_ != '#CVCVirtual_included#') {
    $_CVCVirtual_ = '#CVCVirtual_included#';

    class CVCVirtual extends CVirtual {
      function CVCVirtual () { $this->SetClassName ('CVCVirtual'); }
      function SetDefaultSettings () { $this->SetClassName ('CVCVirtual'); }

      // Return container HTML code
      function InnerHTML () { return ''; }

      // Return full HTML code
      function OuterHTML () {
        return $this->PrefixHTML () . $this->InnerHTML () . $this->PostfixHTML ();
      }

      // Return prefix HTML code
      function PrefixHTML () { return ''; }

      // Return postfix HTML code
      function PostfixHTML () { return ''; }

      function Draw () { print ($this->OuterHTML ()); }
      function FromTemplate ($tpl, $args = array (), $parse = true) {
        return tpl ('back/vclasses/'.$this->GetClassName ().'/'.$tpl,
                    $args, $parse);
      }
    }

    content_Register_VCClass ('CVCVirtual');
  }
?>
