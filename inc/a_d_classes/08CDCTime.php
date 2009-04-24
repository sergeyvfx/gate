<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Implementation of time datatype
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

  if ($_CDCTime_ != '#CDCTime_included#') {
    $_CDCTime_ = '#CDCTime_included#';

    class CDCTime extends CDCVirtual {
      function CDCTime () { $this->SetClassName ('CDCTime'); }

      function DrawEditorForm  ($name, $formname = '', $init = true) {
        global $CORE;
        $CORE->AddStyle ('time');
        tplp ('back/timepicker', array ('name'=>$formname.'_'.$name, 'value'=>$this->GetValue () ));
      }
    }

    content_Register_DCClass ('CDCTime', 'Время');
  }
?>
