<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Implementation of image datatype
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

  if ($_CDCImage_ != '#CDCImage_included#') {
    $_CDCImage_ = '#CDCImage_included#';

    $image_stuff_included = false;
    $image_contentSettingsform_stuff_included = false;

    class CDCImage extends CDCVirtual {
      var $storage = nil;

      function CDCImage () { $this->SetClassName ('CDCImage'); }

      function SpawnStorage () {
        if ($this->storage != nil &&
            $this->storage->GetID () == $this->settings['storage']) {
          return;
        }

        $this->storage = manage_spawn_storage ($this->settings['storage']);
      }

      function Init () { $this->SpawnStorage (); }

      function SettingsForm () {
        print '<span class="shade">Настройки данного класса отсутствуют</span>';
      }

      function DrawLimits ($field, $suff) {
        global $image_contentSettingsform_stuff_included;

        if (!$image_contentSettingsform_stuff_included) {
          println ('<script language="JavaScript" type="text/javascript">
              function img_updateLimits (name, suff) {
                var limit=getElementById ("cntset_"+name+"_"+suff).value;
                if (limit=="")
                  hide ("cntset_"+name+"_"+suff+"_val"); else
                  si ("cntset_"+name+"_"+suff+"_val");
              }
            </script>');

          $image_contentSettingsform_stuff_included = true;
        }

       $limval = substr ($this->settings[$suff], 0, 2);
       $val = substr ($this->settings[$suff], 2, strlen ($this->settings[$suff]) - 2);

       if (isset ($_POST['cntset_'.$field.'_'.$suff])) {
         $limval = $_POST['cntset_'.$field.'_'.$suff];
         $val = $_POST['cntset_'.$field.'_'.$suff.'_val'];
       }

        println ('  <select style="width: 100px; margin-right: 16px;" name="cntset_'.$field.'_'.$suff.'" id="cntset_'.$field.'_'.$suff.'" onchange="img_updateLimits (\''.$field.'\', \''.$suff.'\')">'.
            '<option value=""'.(($limval=='')?(' selected'):('')).'>Нет</option>'.
            '<option value="&lt;="'.(($limval=='<=')?(' selected'):('')).'>&lt;=</option>'.
            '<option value="=="'.(($limval=='==')?(' selected'):('')).'>==</option>'.
            '<option value="&gt;="'.(($limval=='>=')?(' selected'):('')).'>&gt;=</option>'.
          '</select><input type="text"' .(($limval=='')?(' class="txt invisible"'):(' class="txt"')).' name="cntset_'.$field.'_'.$suff.'_val" id="cntset_'.$field.'_'.$suff.'_val" value="'.$val.'">');
      }

      function DrawContentSettingsForm ($title, $field, $titled = true) {
        if ($titled) {
          println ("<b>$title:</b><br>");
        }

        println ('Хранилище данных для хранения изображений:');
        $arr = manage_storage_get_list ();
        $n = count ($arr);

        $sel_storage = $this->settings['storage'];
        if (isset ($_POST['cntset_'.$field.'_storage'])) {
          $sel_storage=$_POST['cntset_'.$field.'_storage'];
        }

        println ('<select name="cntset_'.$field.'_storage" class="block">');

        for ($i=0; $i<$n; $i++) {
          println ('<option value="'.$arr[$i]->GetID ().'"'.
              (($sel_storage==$arr[$i]->GetID ())?(' selected'):('')).
              '>'.$arr[$i]->GetName ().'</option>');
        }

        println ('</select><div id="hr"></div>');
        println ('<table class="clear">');
        println (  '<tr><td width="140">Ограничение по ширине:</td><td style="padding: 2px 0;">'); $this->DrawLimits ($field, 'hlimit'); print ('</td></tr>');
        println (  '<tr><td>Ограничение по высоте:</td><td style="padding: 2px 0;">'); $this->DrawLimits ($field, 'vlimit'); print ('</td></tr>');
        println (  '<tr><td>Ограничение по размеру:</td><td style="padding: 2px 0;">'); $this->DrawLimits ($field, 'size'); print ('</td></tr>');
        println ('</table>');
        return true;
      }

      function UpdateLimitSetting ($arr, $title, $field, $suff) {
        if ($_POST['cntset_'.$field.'_'.$suff]) {
          if (!isnumber ($_POST['cntset_'.$field.'_'.$suff.'_val'])) {
            add_info ('Некорректное ограничение изображения в поле &laquo;'.$title.'&raquo;');
            return false;
          } else {
            $arr[$suff]=$_POST['cntset_'.$field.'_'.$suff].$_POST['cntset_'.$field.'_'.$suff.'_val'];
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

        if (!$this->UpdateLimitSetting (&$res, $title, $field, 'hlimit')) return false;
        if (!$this->UpdateLimitSetting (&$res, $title, $field, 'vlimit')) return false;
        if (!$this->UpdateLimitSetting (&$res, $title, $field, 'size')) return false;

        $this->settings['data']=$res;
        $this->settings=combine_arrays ($this->settings, $res);

        return true;
      }

      function IncludeStuff () {
        global $image_stuff_included;

        if ($image_stuff_included) {
          return;
        }

        print ('
          <script language="JavaScript" type="text/javascript">
            function CDCImage_OnImageUpload (field, formname, url, val, w, h, mime) {
              if (val=="") {
                url = document_root + "/pics/clear.gif";
                hide (formname+"_" + field + "_infoBox");
              } else {
                var n = getElementById (formname + "_" + field + "_infoBox");
                var wn = elementByIdInTree (n, "w");
                var hn = elementByIdInTree (n, "h");
                var mimen = elementByIdInTree (n, "mime");
                wn.innerHTML = w + "px";
                hn.innerHTML = h + "px";
                mimen.innerHTML = mime;
                sb (formname + "_" + field + "_infoBox")
              }
              getElementById (formname + "_" + field).value = val;
              getElementById (formname + "_" + field + "_img").src = url;
              getElementById (formname + "_" + field + "_url").value = url;
            }

            function CDCImage_ZerolizeForm (field, formname) {
              hide (formname + "_" + field + "_infoBox")
              getElementById (formname+"_"+field+"_img").src = \''.config_get ('document-root').'/pics/clear.gif\';
            }
          </script>');

        $image_stuff_included = true;
      }

    function DrawEditorForm ($field, $formname = '', $init = true) {
        $this->IncludeStuff ();
        $arr=array ('field'    => $field,
                    'value'    => $this->GetValue (),
                    'formname' => $formname,
                    'storage'  => $this->settings['storage'],
                    'size'     => $this->settings['size'], 
                    'hlimit'   => $this->settings['hlimit'], 
                    'vlimit'   => $this->settings['vlimit']);

        if ($this->GetValue ()!='') {
          $this->SpawnStorage ();
          $arr['img'] = $this->storage->GetFullURL ($this->GetValue ());
          $arr['params'] = $this->storage->GetFileParams ($this->GetValue ());
        }

        print ($this->FromTemplate ('edit.form', $arr));
      }

      function NewContentSpawned ($field, $content_id = -1) {
        manage_storage_refcount_inc ($this->settings['storage']);
      }

      function PerformContentDeletion ($field, $content_id = -1) {
        manage_storage_refcount_dec ($this->settings['storage']);
      }

      function ReceiveValue ($field, $formname = '') {
        CDCVirtual::ReceiveValue ($field, $formname);
        $this->SpawnStorage ();
        $this->storage->Accept ($this->GetValue ());
      }

      function DestroyValue () {
        $this->SpawnStorage ();
        $this->storage->Unlink ($this->GetValue ());
      }

      function Value () {
        $this->SpawnStorage ();
        return $this->storage->GetFullURL ($this->GetValue ());
      }

      function BuildCheckImportancy ($var, $formname='') {
        return '(getElementById ("'.$formname.'_'.$var.'").value!="")';
      }
    }

    content_Register_DCClass ('CDCImage', 'Изображение');
  }
?>
