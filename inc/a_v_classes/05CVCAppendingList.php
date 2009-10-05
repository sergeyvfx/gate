<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Dinamicallu building list
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

  if ($_CVCAppendingList_ != '#CVCAppendingList_Included#') {
    $_CVCAppendingList_ = '#CVCAppendingList_Included#';

    class CVCAppendingList extends CVCVirtual {
      var $name, $items, $itemsUsed;

      function CVCAppendingList () { $this->SetClassName ('CVCAppendingList'); }

      function Init ($name = '', $settings = '') {
        $this->SetDefaultSettings ();
        $this->contents = array ();
        $this->name = $name;
        $this->items = array ();
        $this->itemsUsed = array ();
        $params = unserialize_params ($settings);
        $this->SetSettings (combine_arrays ($this->GetSettings (), $params));
      }

      function AppendItem ($title, $tag) {
        $this->items[] = array ('title' => $title, 'tag' => $tag);
      }

      function SetDefaultSettings () {
        $this->SetClassName ('CVCAppendingList');
      }

      function InnerHTML () {
        global $CORE, $appending_list_script_printed;

        if (!isset ($appending_list_script_printed)) {
          $CORE->AddScript ( 'language=JavaScript;type=text/JavaScript',
                            $this->FromTemplate ('script', array (), false));
          $appending_list_script_printed = true;
        }

        return $this->FromTemplate ('widget',
                                    array ('name' => $this->name,
                                           'settings' => $this->settings,
                                           'items' => $this->items,
                                           'itemsUsed' => $this->itemsUsed));
      }

      function ReceiveItemsUsed () {
        $s = $_POST['alist_'.$this->name.'_items'];
        $s = preg_replace ('/\r/', '', $s);

        if ($s == '') {
          return array ();
        }

        $this->itemsUsed = explode ("\n", $s);
      }

      function GetItemsUsed () { return $this->itemsUsed; }
      function SetItemsUsed ($items) { $this->itemsUsed = $items; }

      function SetItems ($arr) { $this->items=$arr; }
    }

    function receiveDataFromAList ($name) {
      $list = new CVCAppendingList ();
      $list->Init ($name);
      $list->ReceiveItemsUsed ();
      return $list->GetItemsUsed ();
    }

    content_Register_VCClass ('CVCAppendingList');
  }
?>
