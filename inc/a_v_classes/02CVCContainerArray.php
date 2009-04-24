<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Array of visual containers
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

  if ($_CVCContainerArray_ != '#CVCContainerArray_Included#') {
    $_CVCContainerArray_ = '#CVCContainerArray_Included#';

    class CVCContainerArray extends CVCVirtual {
      var $containers;
      var $containerLinks;

      function CVCContainerArray () { $this->SetClassName ('CVCContainerArray'); }

      function Init () {
        $this->SetDefaultSettings ();
        $this->containers = array ();
        $this->containerLinks = array ();
      }

      function SetDefaultSettings() { $this->SetClassName ('CVCContainerArray'); }

      function InnerHTML () {
        $result = '';

        for ($i=0; $i<count ($this->containers); $i++) {
          $container = $this->containers[$i];
          $result .= $container->OuterHTML ();
        }
        return $result;
      }

      function AppendContainer ($container, $name = '') {
        $this->containerLinks[$container->GetName ()] = &$container;
        $this->containers[] = $container;
      }

      function GetContainerByName ($name) { return $this->containerLinks[$name]; }
      function GetContainerByNumber ($i) { return $this->containers[$i]; }
    }

    content_Register_VCClass ('CVCContainerArray');
  }
?>
