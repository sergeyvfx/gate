<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Implementation of file datatype
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

  if ($_CDCFile_ != '#CDCFile_included#') {
    $_CDCFile_ = '#CDCFile_included#';
    $file_contentSettingsform_stuff_included = false;
    $file_stuff_included = false;

    class CDCFile extends CDCVirtual {
      var $storage = nil;

      function CDCFile () { $this->SetClassName ('CDCFile'); }

      function SpawnStorage () {
        if ($this->storage != nil &&
            $this->storage->GetID () == $this->settings['storage']) {
          return;
        }

        $this->storage = manage_spawn_storage ($this->settings['storage']);
      }

      function Init () { $this->SpawnStorage (); }

      function SettingsForm () { print '<span class="shade">Настройки данного класса отсутствуют</span>'; }

      function DrawLimits ($field, $suff) {
        global $file_contentSettingsform_stuff_included;

        if (!$file_contentSettingsform_stuff_included) {
          println ('<script language="JavaScript" type="text/javascript">
              function file_updateLimits (name, suff) {
                var limit=getElementById ("cntset_" + name + "_" + suff).value;
                if (limit == "")
                  hide ("cntset_" + name + "_" + suff + "_val"); else
                  si ("cntset_" + name + "_" + suff + "_val");
              }
            </script>');
          $file_contentSettingsform_stuff_included = true;
        }

        $s = $suff;

        if ($suff == 'file_size') {
          $s = 'size';
        }

        $limval = substr ($this->settings[$s], 0, 2);
        $val = substr ($this->settings[$s], 2, strlen ($this->settings[$s])-2);

        if (isset ($_POST['cntset_'.$field.'_'.$suff])) {
          $limval = $_POST['cntset_'.$field.'_'.$suff];
          $val = $_POST['cntset_'.$field.'_'.$suff.'_val'];
        }

        println ('  <select style="width: 100px; margin-right: 16px;" name="cntset_'.$field.'_'.$suff.'" id="cntset_'.$field.'_'.$suff.'" onchange="file_updateLimits (\''.$field.'\', \''.$suff.'\')">'.
            '<option value=""'.(($limval=='')?(' selected'):('')).'>Нет</option>'.
            '<option value="&lt;="'.(($limval=='<=')?(' selected'):('')).'>&lt;=</option>'.
            '<option value="=="'.(($limval=='==')?(' selected'):('')).'>==</option>'.
            '<option value="&gt;="'.(($limval=='>=')?(' selected'):('')).'>&gt;=</option>'.
          '</select><input type="text"' .(($limval=='')?(' class="txt invisible"'):(' class="txt"')).' name="cntset_'.$field.'_'.$suff.'_val" id="cntset_'.$field.'_'.$suff.'_val" value="'.$val.'">');
      }

      function DrawContentSettingsForm ($title, $field, $titled=true) {
        if ($title) {
          println ("<b>$title:</b><br>");
        }

        println ('Хранилище данных для хранения файлов:');

        $arr = manage_storage_get_list ();
        $n = count ($arr);

        $sel_storage = $this->settings['storage'];
        if (isset ($_POST['cntset_'.$field.'_storage'])) {
          $sel_storage=$_POST['cntset_'.$field.'_storage'];
        }

        println ('<select name="cntset_'.$field.'_storage" class="block">');
        for ($i = 0; $i < $n; $i++) {
          println ('<option value="'.$arr[$i]->GetID ().'"'.
              (($sel_storage==$arr[$i]->GetID ())?(' selected'):('')).
              '>'.$arr[$i]->GetName ().'</option>');
        }

        println ('</select><div id="hr"></div>');
        println ('<table class="clear">');
        println (  '<tr><td width="140">Ограничение по размеру:</td><td style="padding: 2px 0;">'); $this->DrawLimits ($field, 'file_size'); print ('</td></tr>');
        println ('</table>');
        return true;
      }

      function UpdateLimitSetting ($arr, $title, $field, $sfield, $suff) {
        if ($_POST['cntset_'.$field.'_'.$suff]) {
          if (!isnumber ($_POST['cntset_'.$field.'_'.$suff.'_val'])) {
            add_info ('Некорректное ограничение файла в поле &laquo;'.$title.'&raquo;');
            return false;
          } else {
            $arr[$sfield] = $_POST['cntset_'.$field.'_'.$suff].$_POST['cntset_'.$field.'_'.$suff.'_val'];
          }
        }

        return true;
      }

      function ReceiveContentSettings ($title, $field) {
        $st = $_POST['cntset_'.$field.'_storage'];

        if (!manage_stroage_exists ($st)) {
          add_info ('Не указано хранилище данных для поля &laquo;'.$title.'&raquo;');
          return false;
        }

        $res = array ();
        $res['storage'] = $st;

        if (!$this->UpdateLimitSetting (&$res, $title, $field,
            'size', 'file_size')) {
          return false;
        }

        $this->settings['data']=$res;
        $this->settings = combine_arrays ($this->settings, $res);
        return true;
      }

      function IncludeStuff () {
        global $file_stuff_included;

        if ($file_stuff_included) {
          return;
        }

        println ('
          <script language="JavaScript" type="text/javascript">
            function CDCFile_OnFileUpload (field, formname, url, val, mime, size) {
              if (val=="") { hide (formname + "_" + field + "_infoBox"); } else {
                var n=getElementById (formname + "_" + field + "_infoBox");
                var mimen=elementByIdInTree (n, "mime");
                var sizen=elementByIdInTree (n, "size");
                mimen.innerHTML = mime;
                sizen.innerHTML = size;
                sb (formname + "_" + field + "_infoBox")
              }
              getElementById (formname+"_" + field).value = val;
              getElementById (formname+"_" + field + "_url").value = url;
            }

            function CDCFile_ZerolizeForm (field, formname) {
              hide (formname + "_" + field + "_infoBox")
            }
          </script>');

        $file_stuff_included = true;
      }

     function DrawEditorForm  ($field, $formname='', $init=true) {
      $this->IncludeStuff ();
      $arr=array ('field'    => $field,
                  'value'    => $this->GetValue (),
                  'formname' => $formname,
                  'storage'  => $this->settings['storage'],
                  'size'     => $this->settings['size']);
      if ($this->GetValue ()!='') {
        $this->SpawnStorage ();
        $arr['file']=$this->storage->GetFullURL ($this->GetValue ());
        $arr['params']=$this->storage->GetFileParams ($this->GetValue ());
      }
      print ($this->FromTemplate ('edit.form', $arr));
     }

      function NewContentSpawned ($field, $content_id = -1) {
        manage_storage_refcount_inc ($this->settings['storage']);
      }

      function PerformContentDeletion ($field, $content_id = -1) {
        manage_storage_refcount_dec ($this->settings['storage']);
      }

      function Comporator ($val, $key, $field, $type, $dimension) {
        $r = smartcmp ($val, $key);
        $eq = ($type == 0) ? ('равна') : ('равен');

        if ($r == 'COMPILES')    return '';
        if ($r == 'NOTCOMPILES') return $field.' файла не '.$eq.' '.parseint ($key).' '.$dimension.'.';
        if ($r == 'GREATER')     return $field.' файла превосходит '.parseint ($key).' '.$dimension.'.';
        if ($r == 'LESS')        return $field.' файла меньше '.parseint ($key).' '.$dimension.'.';
      }

      function ReceiveValue ($field, $formname='') {
        $this->SpawnStorage ();
        $data = $_FILES[$formname.'_'.$field];
        $r = $this->Comporator ($data['size'], $this->settings['size'],
          'Размер', 1, 'байт');

        if ($r != '') {
          add_info ($r);
          return false;
        }

        $fn = $this->storage->Put ($data, user_id ());
        $this->storage->Accept ($fn);
        $this->val=$fn;
        return true;
      }

      function DestroyValue () {
        $this->SpawnStorage ();
        $this->storage->Unlink ($this->GetValue ());
      }

      function Value () {
        $this->SpawnStorage ();
        return $this->storage->GetFullURL ($this->GetValue ());
      }

      function BuildCheckImportancy ($var, $formname = '') {
        return '(getElementById ("'.$formname.'_'.$var.'").value != "")';
      }
    }

    content_Register_DCClass ('CDCFile', 'Файл');
  }
?>
