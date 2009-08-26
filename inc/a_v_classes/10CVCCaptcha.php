<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * CAPTCHA
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

  if ($_CVCCaptcha_ != '#CVCCaptcha_Included#') {
    $_CVCaptcha_ = '#CVCCaptcha_Included#';

    class CVCCaptcha extends CVCVirtual {
      function CVCCaptcha () { $this->SetClassName ('CVCCaptcha'); }

      function Init ($name = '', $settings = '') {
        $this->SetDefaultSettings ();
        $this->contents = array ();

        $params = unserialize_params ($settings);
        $this->SetSettings (combine_arrays ($this->GetSettings (), $params));
      }

      function SetDefaultSettings() { $this->SetClassName ('CVCCaptcha'); }

      function InnerHTML () {
        return '<img src="'.config_get ('document-root').'/inc/stuff/captcha/data.php">';
      }
    }

    content_Register_VCClass ('CVCCaptcha');
  }
?>
