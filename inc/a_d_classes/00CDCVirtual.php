<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Virtual class for data types classes
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

  if ($_CDCVirtual_ != '#CDCVirtual_included#') {
    $_CDCVirtual_ = '#CDCVirtual_included#';

    class CDCVirtual extends CVirtual {
      var $val;

      function FromTemplate ($tpl,$args=array ()) {
        return tpl ('back/dclasses/'.$this->GetClassName ().'/'.$tpl,$args);
      }

      function SettingsForm () {
        print '<span class="shade">Настройки данного класса отсутствуют</span>';
      }

      function CheckConfigScript  ()  { return ''; }
      function ReceiveSettings    ()  { return true; }

      function DrawEditorForm ($field, $formname = '', $init = true) { }

      function ReceiveValue   ($field, $formname = '') {
        $this->val = stripslashes ($_POST[$formname.'_'.$field]);
      }

      function BuildQueryValue      ()     { return '"'.addslashes ($this->val).'"'; }
      function BuildCheckImportancy ($var) { return ('(true)'); }

      function GetDBFieldType () { return 'TEXT'; }

      function DrawContentSettingsForm ($title, $field) { return false; }
      function ReceiveContentSettings  ($title, $field) { return true;  }

      function NewContentSpawned ($field, $content_id = -1) { }
      function ContentDeleted    ($field, $content_id = -1) { }

      function SetValue ($v) { $this->val = $v; }
      function GetValue ()   { return $this->val; }

      function PerformContentDeletion ($field, $content_id = -1) {  }

      function FreeValue ()    { $this->SetValue (''); }
      function DestroyValue () {  }

      function Value () { return $this->val; }

      function BuildInitScript ($field, $formname='') { return ''; }
    }
  }
?>
