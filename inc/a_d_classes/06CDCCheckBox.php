<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Implementation of checkbox datatype
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

  if ($_CDCCheckBox_ != '#CDCCheckBox_included#') {
    $_CDCCheckBox_ = '#CDCCheckoBox_included#';

    class CDCCheckBox extends CDCVirtual {
      function CDCCheckBox () { $this->SetClassName ('CDCCheckBox'); }

      function DrawEditorForm ($name, $formname = '', $init = true) {
        $value = $this->val;
        $dummy = $formname.'_'.$name;
        println ('<div><input type="checkbox" name="'.$dummy.'"'.
            (($value)?(' checked'):('')).' class="cb">&nbsp;Активен</div>');
      }

      function ReceiveValue ($field, $formname = '') {
        if ($_POST[$formname.'_'.$field]) {
          $this->val=true;
        } else {
          $this->val=false;
        }
      }

      function BuildQueryValue () {
        $v = 'TRUE';

        if (!$this->val) {
          $v='FALSE';
        }

        return "$v";
      }

      function GetDBFieldType () {
        return 'BOOL';
      }
    }

    content_Register_DCClass ('CDCCheckBox', 'Флажок');
  }
?>
