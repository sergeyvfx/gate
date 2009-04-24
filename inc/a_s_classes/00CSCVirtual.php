<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Virtual class for services
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

  if ($_CSCVirtual_included_ != '#CSCVirtual_Included#') {
    $_CSCVirtual_included_='#CSCVirtual_Included#';

    class CSCVirtual extends CVirtual {
      var $sName, $id;

      function CSCVirtual () {
        $this->SetServiceName ('CSCVirtual');
        $this->SetClassName ('CSCVirtual');
      }

      function CanCreate () { return true; }
      function Create () {  }
      function InitInstance ($id = -1, $virtual = false) {  }
      function PerformDeletion () {  }

      function DrawSettingsForm ($formnane = '') {
        print ('<span class="shade">Для сервиса &laquo;<b>'.
          $this->GetServiceName ().'</b>&raquo; настройки отсутствуют</span>');
      }

      function ReceiveSettings ($formnane = '') { return true; }
      function SetServiceName ($v) { $this->sName=$v; }
      function GetServiceName ()   { return $this->sName; }

      function UpdateSettings () {
        $settings = addslashes ($this->SerializeSettings ());
        db_update ('service', array ('settings'=>"\"$settings\""), '`id`='.$this->id);
      }
    }
  }
?>
