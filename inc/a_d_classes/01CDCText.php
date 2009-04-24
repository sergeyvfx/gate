<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Implementation of text datatype
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

  if ($_CDCText_ != '#CDCText_included#') {
    $_CDCText_ = '#CDCText_included#';

    class CDCText extends CDCVirtual {
      function CDCText () { $this->SetClassName ('CDCText'); }

      function SetDefaultSettings () {
        $this->SetSetting ('nlines', '1');
      }

      function SettingsForm () {
?>
Количество строк:
<input type="text" class="txt block" id="CDCText_nLines"name="CDCText_nLines" value="<?=htmlspecialchars (stripslashes ($_POST['CDCText_nLines']));?>">
<?php
      }

      function CheckConfigScript () {
        return ('if (!isnumber (getElementById ("CDCText_nLines").value)) {alert ("Указанное количество строк не является правильным натуральным числом."); return false;}'."\n".
          'if (getElementById ("CDCText_nLines").value<1) {alert ("Минимально возможное количество строк - одна."); return false;}');
      }

      function ReceiveSettings () {
        global $_POST;
        $this->SetSetting ('nlines', $_POST['CDCText_nLines']);
        return true;
      }

      function DrawEditorForm ($name, $formname = '', $init = true) {
        $value = htmlspecialchars (ecranvars ($this->val));
        $dummy = $formname.'_'.$name;

        if ($this->settings['nlines'] == 1) {
          print ("<input type=\"text\" class=\"txt block\" id=\"$dummy\" name=\"$dummy\" value=\"$value\">");
        } else {
          $nlines = $this->settings['nlines'];
          print ("<textarea class=\"block\" name=\"$dummy\" id=\"$dummy\" rows=\"$nlines\">$value</textarea>");
        }
      }

      function BuildCheckImportancy ($var, $formname = '') {
        $dummy = $formname.'_'.$var;
        return "(qtrim (getElementById ('$dummy').value)!='')";
      }

      function GetDBFieldType       ()                   { return 'LONGTEXT'; }
    }

    content_Register_DCClass ('CDCText', 'Текстовое поле');
  }
?>
