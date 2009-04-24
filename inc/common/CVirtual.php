<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Base virtual class
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

  if ($_CVirtual_ != '#CVirtual_included#') {
    $_CVirtual_ = '#CVirtual_included#';

    class CVirtual {
      var $className;
      var $settings = array ();

      function Init () { $this->SetDefaultSettings (); }

      // Get className
      function GetClassName ()   { return $this->className; }

      // Set className
      function SetClassName ($v) { $this->className=$v; }

      // Get all settings
      function GetSettings ()   { return $this->settings; }

      // Set settings
      function SetSettings ($s) {
        if (!is_array ($s)) {
          return;
        }

        foreach ($s as $k => $v) {
          $this->settings[$k] = $v;
        }
      }

      // Sets the default settings
      function SetDefaultSettings () {
        $this->settings = array ();
      }

      // Get single setting
      function GetSetting ($s) { return $this->settings[$s]; } 

      // Set single setting
      function SetSetting ($s, $v) { $this->settings[$s] = $v; }

      function UpdateSettings ($s) {
        if (!is_array ($s)) {
          return;
        }

        foreach ($s as $k => $v) {
          $this->settings[$k] = $v;
        }
      }

      function SerializeSettings () { return serialize ($this->settings); }

      function UnserializeSettings ($s) {
        $settings = unserialize ($s);

        if (is_array (&$settings)) {
          $this->settings=$settings;
        } else {
          $this->SetDefaultSettings ();
        }
      }
    }

    content_Register_VCClass ('CVCVirtual', '');
  }
?>
