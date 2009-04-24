<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Dataset for Wiki pages
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

  if ($_manage_ds_included_ != '#manage_ds_Included#') {
    $_manage_ds_included_ = '#manage_ds_Included#'; 

    global $manage_dataset_selector_stuffed;

    $manage_dataset_cache = array ();

    class CDataSet extends CVirtual {
      var $id, $name, $fields;

      function CDataSet () { $this->SetClassName ('CDataSet'); }

      function Init ($id = -1, $settings = array ()) {
        if ($id == '') {
          $id=-1;
        }

        if ($q = db_select ('dataset', array ('id', 'name'),
                            "`id`=$id")) {
          $this->id = $id;
        } else {
          $this->id = -1;
        }

        $this->UpdateSettings ($settings);
        if ($this->id > 0) {
          $r = db_row ($q);
          $this->name = $r['name'];
          $this->InitFields ();
        }
      }

      function InitFields () {
        $q = db_select ('dataset_assoc', array ('id'),
                        '`dataset`='.$this->id, 'ORDER BY `order`');
        $this->fields = array ();

        while ($r = db_row ($q)) {
          $c = new CDataField ();
          $c->Init ($r['id']);
          $c->UpdateDataSettings ($this->settings['fields'][$c->GetField ()]);
          $this->fields[] = $c;
        }
      }

      function UpdateFieldSettings () {
        $n = count ($this->fields);

        for ($i = 0; $i < $n; $i++) {
          $this->fields[$i]->UpdateDataSettings ($this->settings['fields'][$this->fields[$i]->GetField ()]);
        }
      }

      function SetSettings ($s) {
        CVirtual::SetSettings ($s);
        $this->UpdateFieldSettings ();
      }

      function RefCount () { return manage_dataset_refcount ($this->id); }
      function Ref      () { return manage_dataset_refcount_inc ($this->id); }
      function Unref    () { return manage_dataset_refcount_dec ($this->id); }

      function Create () {
        $name = addslashes (htmlspecialchars ($this->name));

        if ($name == '') {
          return;
        }

        if (db_count ('dataset', '`name`="'.$name.'"') <= 0) {
          db_insert ('dataset', array ('name' => "\"$name\"", 'refcount' => 0));
          return true;
        } else {
          add_info ('Набор данных с таким именем уже существует.');
          return false;
        }
      }

      function CreateReceived () {
        $this->SetName (stripslashes ($_POST['name']));
        if ($this->Create ()) {
          $_POST=array ();
        }
      }

      function UpdateReceived () {
        global $_POST;
        $n = trim ($_POST['className']);
        $name = htmlspecialchars (trim ($_POST['className']));

        if ($name == '') {
          return;
        }

        if (db_count ('dataset', '`name`="'.$name.'" AND `id`<>'.$this->id) > 0) {
          add_info ('Набор данных с таким именем уже существует.');
        } else {
          db_update ('dataset', array ('name'=>"\"$name\""), '`id`='.$this->id);
          $this->name = stripslashes (trim ($_POST['className']));
        }
      }

      function Destroy () {
        if ($this->RefCount () > 0) {
          return;
        }

        for ($i = 0; $i < count ($this->fields); $i++) {
          $this->fields[$i]->Destroy ();
        }

        db_query ('DELETE FROM `dataset` WHERE `id`='.$this->id);
      }

      function AppendField ($type, $title, $field) {
        if (manage_datafield_create ($this->id, $type, $title, $field, &$c)) {
          $fields[] = $c;
          return true;
        }

        return false;
      }

      function AppendReceivedField () {
        global $_POST;

        if ($this->RefCount () > 0) {
          return;
        }

        $title = trim (stripslashes ($_POST['ftitle']));
        $field = stripslashes ($_POST['field']);
        $type  = stripslashes ($_POST['datatype']);

        if ($this->AppendField ($type, $title, $field)) {
          $_POST = array ();
        }

        return true;
      }

      function DeleteFieldById ($id) {
        if ($this->RefCount () > 0) {
          return;
        }

        for ($i = 0; $i < count ($this->fields); $i++) {
          if ($this->fields[$i]->GetID ()==$id) {
            $this->fields[$i]->Destroy ();
            unset ($this->fields[$i]);
          }
        }
      }

      function UpdateField ($id, $title) {
        $r = manage_datafield_update ($id, $title, &$c);

        for ($i = 0; $i < count ($this->fields); $i++) {
          if ($this->fields[$i]->GetID () == $id) {
            $this->fields[$i] = $c;
          }
        }

        return $r;
      }

      function UpdateReceivedField ($id) {
        $r = manage_datafield_update_received ($id, &$c);

        for ($i = 0; $i < count ($this->fields); $i++) {
          if ($this->fields[$i]->GetID () == $id) {
            $this->fields[$i] = $c;
          }
        }

        return $r;
      }

      function ToggleFieldImportancy ($id) {
        return manage_datafield_toggle_importancy ($id);
      }

      function ToggleFieldinvisibility ($id) {
        return manage_datafield_toggle_elem_invisibility ($id);
      }

      function UpField   ($id) { return manage_datafield_up ($id); }
      function DownField ($id) { return manage_datafield_down ($id); }

      function GetName () { return $this->name; }
      function SetName ($name) { $this->name = trim ($name); }

      function Fields () { return $this->fields; }
      function FieldN ($n) { return $this->fields[$n]; }

      function Field ($name) {
        for ($i = 0; $i < count ($this->fields); $i++) {
          if ($this->fields[$i]->GetField () == $name) {
            return $this->fields[$i];
          }
        }

        return null;
      }

      function FieldValue ($name) {
        $c = $this->Field ($name);

        if (!$c) {
          return '';
        }

        return $c->Value ();
      }

      function GenCreateFields () {
        $arr = array ();

        for ($i = 0; $i < count ($this->fields); $i++) {
          $f = $this->fields[$i];
          $arr[$f->GetField ()] = $f->GetDBFieldType ();
        }

        return $arr;
      }

      function ReceiveSettings ($formname='') {
        $n = count ($this->fields);
        $printed = false;

        for ($i = 0; $i < $n; $i++) {
          $f = $this->fields[$i];
          $s = $f->ReceiveContentSettings ($formname.$this->GetID ());

          if (!$s) {
            return false;
          }

          $tmp = $f->GetDataSettings ($formname);

          if (count ($tmp)) {
            $this->settings['fields'][$f->GetField ()]=$tmp;
          }
        }

        return true;
      }

      function DrawSettingsForm ($formname = '') {
        $n = count ($this->fields);
        print ('<div id="hr"></div>');

        for ($i=0; $i<$n; $i++) {
          $f = $this->fields[$i];
          if ($f->DrawContentSettingsForm ($formname.$this->GetID ())) {
            print ('<div id="hr"></div>');
          }
        }
      }

      function DrawEditorForm ($name = '', $url = '.?',
                             $init = true, $title = 'Сохранить') {
        $n = count ($this->fields);
?>
     <script language="JavaScript" type="text/javascript">
        function <?=$name;?>_check (frm) {
<?php
        for ($i = 0; $i < $n; $i++) {
          $f = $this->fields[$i];

          if ($f->GetInvisibility ()) {
            continue;
          }

          if ($f->GetImportancy ()) {
?>
        if (!<?=$f->BuildCheckImportancy ($name);?>) { alert ('Пропущено обязательное поле "<?=$f->GetTitle ();?>"'); return false};
<?php } }?>
          submit (frm);
        }
      </script>
     <form action="<?=$url;?>&action=save" method="POST" name="<?=$name?>" onsubmit="<?=$name?>_check (this); return false;" enctype="multipart/form-data">
<?php
        $printed = false;

        for ($i = 0; $i < $n; $i++) {
          if ($this->fields[$i]->GetInvisibility ()) {
            continue;
          }

          if ($printed) {
            print ('<div id="hr"></div>');
          }

          print ('<b>'.$this->fields[$i]->GetTitle ().'</b>');
          print ($this->fields[$i]->DrawEditorForm ($name, $init)."\n");
          $printed = true;
        }
?>
     <script language="JavaScript" type="text/javascript">
       function submit (frm) {
<?
    $tmp = handler_get_list ('editor_form');
    $arr = $tmp['onsubmit'];
    for ($i = 0; $i < count ($arr); $i++) { ?>
         <?=handler_build_callback ($arr[$i]);?>

<? } ?>
         frm.submit ();
       }
      </script>
     <div class="formPast"><button class="submitBtn block" type="submit"><?=$title;?></button></div>
     </form>
<?php
      }

      function BuildInitScript ($formname = '') {
        $n = count ($this->fields);
        $res = '';

        for ($i = 0; $i < $n; $i++) {
          if ($this->fields[$i]->GetInvisibility ()) {
            continue;
          }

          $tmp = $this->fields[$i]->BuildInitScript ($formname);
          if ($tmp != '') {
            $res.=$tmp."\n";
          }
        }
        return $res;
      }

      function ReceiveData ($formname = '') {
        for ($i = 0; $i < count ($this->fields); $i++) {
          $this->fields[$i]->ReceiveValue ($formname);
        }
      }

      function SetFieldValues ($arr) {
        for ($i = 0; $i < count ($this->fields); $i++) {
          $this->fields[$i]->SetValue ($arr[$this->fields[$i]->GetField ()]);
        }
      }

      function GetFieldValues ($stringify = false, $parse = false) {
        $arr = array ();

        for ($i = 0; $i < count ($this->fields); $i++) {
          if (!$parse) {
            $val= $this->fields[$i]->GetValue ($arr[$this->fields[$i]->GetField ()]);
          } else {
            $val = $this->fields[$i]->Value ($arr[$this->fields[$i]->GetField ()]);
          }

          if ($stringify) {
            $val='"'.addslashes ($val).'"';
          }

          $arr[$this->fields[$i]->GetField ()]=$val;
        }

        return $arr;
      }

      function BuildCompareQuery () {
        $res = '';

        for ($i = 0; $i < count ($this->fields); $i++) {
          if ($res != '') {
            $res.=' AND ';
          }
          $res .= '`'.$this->fields[$i]->GetField ().'`='.
                  $this->fields[$i]->BuildQueryValue ();
        }
        return $res;
      }

      function NewContentSpawned ($id = -1) {
        for ($i = 0; $i < count ($this->fields); $i++) {
          $this->fields[$i]->NewContentSpawned ($id);
        }
      }

      function FreeValues () {
        for ($i = 0; $i < count ($this->fields); $i++) {
          $this->fields[$i]->FreeValue ();
        }
      }

      function PerformContentDeletion ($id) {
        for ($i = 0; $i < count ($this->fields); $i++) {
          $this->fields[$i]->PerformContentDeletion ($id);
        }
      }

      function FreeContent () {
        for ($i = 0; $i < count ($this->fields); $i++) {
          $this->fields[$i]->FreeContent ();
        }
      }

      function GetID () { return $this->id; }
    }

    function manage_spawn_dataset ($id = -1, $settings = array (), $name = '') {
      $c = new CDataSet ();
      $c->Init ($id, $settings);

      if ($id<0) {
        $c->SetName ($name);
      }

      return $c;
    }

    function manage_dataset_refcount ($id) {
      return db_field_value ('dataset', 'refcount', "`id`=$id");
    }

    function manage_dataset_refcount_inc ($id) {
      db_update ('dataset', array ('refcount'=>'`refcount`+1'), "`id`=$id");
    }

    function manage_dataset_refcount_dec ($id) {
      db_update ('dataset', array ('refcount'=>'`refcount`-1'), "`id`=$id");
    }

    function manage_dataset_delete ($id) {
      $c = manage_spawn_dataset ($id); $c->Destroy ();
    }

    function manage_dataset_update_received ($id)   {
      $c = manage_spawn_dataset ($id);
      $c->UpdateReceived ();
    }

    function manage_dataset_create ($name) {
      $c = manage_spawn_dataset (-1, array (), $name);
      return $c->Create ();
    }

    function manage_dataset_received_create () {
      $c = manage_spawn_dataset (-1);
      return $c->CreateReceived ();
    }

    function manage_dataset_append_field ($dataset, $type, $title, $field) {
      $c = manage_spawn_dataset ($dataset);
      $c->AppendField ($type, $title, $field);
    }

    function manage_dataset_append_received_field ($dataset) {
      $c = manage_spawn_dataset ($dataset);
      $c->AppendReceivedField ();
    }

    function manage_dataset_save_field ($id, $eid) {
      $c = manage_spawn_dataset ($id);
      $c->UpdateReceivedField ($eid);
    }

    function manage_dataset_delete_field ($id, $eid) {
      global $eid;
      $c = manage_spawn_dataset ($id);
      $c->DeleteFieldById ($eid);
    }

    function manage_dataset_toggle_elem_importancy ($id, $eid) {
      $c = manage_spawn_dataset ($id);
      $c->ToggleFieldImportancy ($eid);
    }

    function manage_dataset_toggle_elem_invisibility ($id, $eid) {
      $c = manage_spawn_dataset ($id);
      $c->ToggleFieldinvisibility ($eid);
    }

    function manage_dataset_get_fields ($id) {
      return arr_from_query ('SELECT * FROM `dataset_assoc` WHERE `dataset`='.$id.' ORDER BY `order`');
    }

    function manage_dataset_get_list ()  { 
      global $manage_dataset_cache;

      if (isset ($manage_dataset_cache['DataSet.List'])) {
        return $manage_dataset_cache['DataSet.List'];
      }

      $arr = array ();
      $q = db_select  ('dataset', array ('id'), '', 'ORDER BY `name`');

      while ($r = db_row ($q)) {
        $arr[] = manage_spawn_dataset ($r['id']);
      }

      $manage_dataset_cache['DataSet.List'] = $arr;
      return $arr;
    }

    function manage_dataset_up_field ($dataset, $id) {
      $c = manage_spawn_dataset ($dataset);
      $c->UpField ($id);
    }

    function manage_dataset_down_field ($dataset, $id) {
      $c = manage_spawn_dataset ($dataset);
      $c->DownField ($id);
    }

    function manage_dataset_selector_for_content ($prefix = '', $suffix = '', $active = -1) {
      return tpl ('back/dataset_selector', array ('prefix' => $prefix,
          'suffix' => $suffix, 'active' => $active));
    }

    function manage_draw_dataset_selector_for_content ($prefix = '', $suffix = '', $active = -1) {
      println ('Набор данных:');
      println (manage_dataset_selector_for_content ($prefix, $suffix, $active));
    }

    function manage_receive_dataset_id_from_selector ($prefix = '', $suffix = '') {
      if ($prefix) {
        $prefix .= '_';
      }

      if ($suffix) {
        $suffix = '_'.$suffix;
      }

      $id = $_POST[$prefix.'dataset_selector'.$suffix];

      if ($id == '') {
        return -1;
      }

      return $id;
    }

    function manage_receive_dataset_from_selector ($prefix = '', $suffix = '') {
      $id = manage_receive_dataset_id_from_selector ($prefix, $suffix);

      if ($id < 0) {
        return null;
      }

      $dataset = manage_spawn_dataset ($id);

      if (!$dataset->ReceiveSettings ($prefix.$suffix)) {
        return null;
      }

      return $dataset;
    }
  }
?>
