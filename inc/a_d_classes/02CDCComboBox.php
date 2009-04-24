<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Implementation of combo-box datatype
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

  if ($_CDCComboBox_ != '#CDCComboBox_included#') {
    $_CDCComboBox_ = '#CDCComboBox_included#';

    class CDCComboBox extends CDCVirtual {
      function CDCComboBox () { $this->SetClassName ('CDCComboBox'); }

      function SettingsForm () {
?>
      Варианты. Каждая строка - новый вариант. Пустые строки игнорируются:
      <textarea name="CDCComboBox_items" class="block" rows="5"><?=htmlspecialchars (stripslashes ($_POST['CDCComboBox_items']));?></textarea>
<?
      }

      function ReceiveSettings () {
        $items = stripslashes ($_POST['CDCComboBox_items']);
        $items = explode ("\n", $items);
        $arr = array ();

        for ($i = 0; $i < count ($items); $i++) {
          $dummy = trim ($items[$i]);

          if ($dummy!='') {
            $arr[] = $dummy;
          }
        }

        $this->settings['items'] = $arr;
      }

      function DrawEditorForm ($name, $formname = '', $init = true) {
        $value = $this->val;
        $items = $this->settings['items'];
        $dummy = $formname.'_'.$name;

        println ('<select name="'.$dummy.';?>" class="block">');
        for ($i = 0; $i < count ($items); $i++) {
          $it = $items[$i];
          println ('<option value="'.htmlspecialchars ($it).'"'.
            (($it==$value)?(' selected'):('')).'>'.$it.'</option>');
        }
        println ('</select>');
      }

      function BuildCheckImportancy ($var, $formname = '') {
        $dummy = $formname.'_'.$var;
        return "(qtrim (getElementById ('$dummy').value)!='')";
      }

      function GetDBFieldType    () { return 'LONGTEXT'; }
    }

    content_Register_DCClass ('CDCComboBox', 'Выпадающий список');
  }
?>
