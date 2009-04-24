<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Visual content class
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

  if ($_CVCContent_ != '#CVCContent_Included#') {
    $_CVCContent_ = '#CVCContent_Included#';

    class CVCContent extends CVCVirtual {
      var $metas;
      var $scripts;
      var $CSStyles;
      var $contents;

      function CVCContent () { $this->SetClassName ('CVCContent'); }

      function Init () {
        $this->SetDefaultSettings ();
        $this->contents = array ();
      }

      function SetDefaultSettings() { $this->SetClassName ('CVCPage'); }

      function TPrint ($text) { $this->contents[]=array ('type'=>'text', 'text'=>$text); }

      function InnerHTML () {
        $result = '';

        for ($i = 0; $i < count ($this->contents); $i++) {
          $content = $this->contents[$i];
          if ($content['type'] == 'text') {
            $result .= $content['text'];
          }
        }

        return $result;
      }

      function Free () { $this->contents = array (); }
    }

    content_Register_VCClass ('CVCContent');
  }
?>
