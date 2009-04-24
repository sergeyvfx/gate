<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Calendar widget implemetntation
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

  if ($_Calendar_included != '##calendar_Included##') {
    $_Calendar_included = '##calendar_Included##';
    $calendar_sutff_included = false;

    function calendar_include_stuff () {
      global $calendar_sutff_included, $CORE;

      if ($calendar_sutff_included) {
        return;
      }

      $CORE->AddStyle ('calendar');
      $CORE->AddScriptFile ('calendar.js');

      return true;
    }

    function calendar ($name = '', $date = '') {
      calendar_include_stuff ();

      if ($date == '') {
        $date=date ('Y-m-d');
      }

      tplp ('back/calendar', array ('name'=>$name, 'date'=>$date));
      add_body_handler ('onload', 'calendar_Init', array ('"'.$name.'"'));
    }
  }
?>
