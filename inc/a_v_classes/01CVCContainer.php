<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Visual container class
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

  if ($_CVCContainer_ != '#CVCContainer_Included#') {
    $_CVCContainer_ = '#CVCContainer_included#';

    class CVCContainer extends CVCVirtual {
      var $name;
      var $node;
      function CVCContainer () { $this->SetClassName ('CVCContainer'); }

      function Init ($name='') {
        $this->SetDefaultSettings ();

        if ($name!='') {
          $this->SetName ($name);
        }

        $this->node=null;
      }

      function SetDefaultSettings () {
        $this->SetClassName ('CVCContainer');
        $this->name='';
      }

      function GetName () { return $this->name; }
      function SetName ($v) { $this->name=$v; }

      function PrefixHTML () {
        return '<div style="border: 1px red solid;">';
      }

      function PostfixHTML () {
        return '</div>';
      }

      function InnerHTML () {
        if ($this->node != null) {
          return $this->node->OuterHTML ();
        }

        return '';
      }

      function GetNode () {return $this->node;}
      function SetNode ($node) {$this->node = &$node;}
    }

    content_Register_VCClass ('CVCContainer');
  }
?>
