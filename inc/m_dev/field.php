<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Field of Wiki datatype
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

  if ($_manage_field_included_ != '#manage_dield_Included#') {
    $_manage_field_included_ = '#manage_dield_Included#'; 

    $manage_datafiled_denied_fieldnames = array ('id', 'user_id', 'ip');

    class CDataField extends CVirtual {
      var $dataset;
      var $datatype;
      var $title;
      var $field;
      var $value;

      function CDataField () { $this->SetClassName ('CDataField'); }

      function UpdateSettings ($arr) {
        if (!is_array ($arr)) {
          return;
        }

        foreach ($arr as $k => $v) {
          $this->settings[$k] = $v;
        }

        $this->datatype->UpdateSettings ($this->settings);
      }

      function Init ($id) {
        if ($q = db_select ('dataset_assoc', array ('*'), "`id`=$id")) {
          $this->id = $id;
        } else {
          $this->id = -1;
        }

        if ($this->id > 0) {
          $r = db_row ($q);
          $this->datatype = new CDataType ();
          $this->datatype->Init ($r['datatype']);
          $this->title = $r['title'];
          $this->field = $r['field'];
          $this->UnserializeSettings ($r['settings']);
          $this->datatype->UpdateSettings ($this->settings);
        }
      }

      function Create () {
        global $manage_datafiled_denied_fieldnames;
        $dataset = $this->dataset;
        $type = $this->datatype;
        $title = $this->title;
        $field = $this->field;

        if (trim ($title) == '' || trim ($field) == '' ||
            trim ($type) == '') {
              return false;
        }

        if (db_count ('dataset_assoc', '`dataset`="'.$this->id.
                      '" and `title`="'.$title.'"') > 0) {
          add_info ('Поле с таким именем уже существует в этом наборе данных.');
          return false;
        }

        if (db_count ('dataset_assoc', '`dataset`="'.$dataset.
                      '" and `field`="'.$field.'"')>0) {
          add_info ('Поле с таким именем поля в базе данных уже '.
                    'существует в этом наборе данных.');
          return false;
        }

        if (!isalphanum ($field)) {
          add_info ('Название поля может содержать лишь '.
                    'символы латинского алфавита и цифры.');
          return false;
        }

        // Check da valide of the fieldname
        $valid = true;
        for ($i = 0; $i < count ($manage_datafiled_denied_fieldnames); $i++) {
          if ($manage_datafiled_denied_fieldnames[$i]==$field) {
            $valid=false;
            break;
          }
        }

        if (!$valid) {
          add_info ('Извините, но данное название поля является '.
                    'системным и Вы не можете его использрвать.');
          return false;
        }

        $title = htmlspecialchars ($title);

        $mord = db_max ('dataset_assoc', 'order', '`dataset`='.$dataset);
        $ord = $mord + 1;
        $s = array ('important' => false, 'invisible' => false);
        $s = addslashes (serialize ($s));
        db_insert ('dataset_assoc', array ('dataset'=>'"'.$dataset.'"',
                   'datatype'=>"\"$type\"", 'title'=>"\"$title\"",
                   'field'=>"\"$field\"", 'settings'=>"\"$s\"", 'order'=>$ord));
        manage_datatype_refcount_inc ($type);
        return true;
      }

      function Destroy () {
        $dataset = db_field_value ('dataset_assoc', 'dataset',
                                   "`id`=".$this->id);

        if (manage_dataset_refcount ($dataset) > 0) {
          return;
        }

        $datatype = db_field_value ('dataset_assoc', 'datatype',
                                    "`id`=".$this->id);
        manage_datatype_refcount_dec ($datatype);
        db_query ('DELETE FROM `dataset_assoc` WHERE `id`='.$this->id);
      }

      function Update ($title) {
        global $_POST;
        $t = $title;
        $title = htmlspecialchars (trim ($_POST['title']));

        if ($title == '') {
          return;
        }

        $dataset = db_field_value ('dataset_assoc', 'dataset',
                                   '`id`='.$this->id);

        if (db_count ('dataset_assoc', '`dataset`="'.$dataset.
                      '" and `title`="'.$title.'" AND `id`<>'.$this->id) > 0) {
          add_info ('Поле с таким именем уже существует в этом наборе данных.');
          return false;
        }

        db_update ('dataset_assoc', array ('title'=>"\"$title\""),
                   '`id`='.$this->id);
        $this->title = $t;

        return true;
      }

      function UpdateReceived () {
        return $this->Update (stripslashes ($_POST['title']));
      }

      function UpdateStoredSettings () {
        db_update ('dataset_assoc', array ('settings' => '"'.
          addslashes ($this->SerializeSettings ()).'"'), '`id`='.$this->id);
      }

      function ToggleImportancy () {
        $this->settings['important'] = !$this->settings['important'];

        if ($this->settings['important']) {
          $this->settings['invisible'] = false;
        }

        $this->UpdateStoredSettings ();
      }

      function ToggleInvisibility () {
        $this->settings['invisible'] = !$this->settings['invisible'];

        if ($this->settings['invisible']) {
          $this->settings['important'] = false;
        }

        $this->UpdateStoredSettings ();
      }

      function Up   () {
        $dataset = db_field_value ('dataset_assoc', 'dataset', '`id`='.$this->id);
        db_move_up ('dataset_assoc', $this->id, '`dataset`='.$dataset);
      }

      function Down () {
        $dataset = db_field_value ('dataset_assoc', 'dataset', '`id`='.$this->id);
        db_move_down ('dataset_assoc', $this->id, '`dataset`='.$dataset);
      }

      function GetDataset  () { return $this->dataset; }
      function GetDatatype () { return $this->datatype; }
      function GetDataClass() { return $this->datatype->GetDataClass (); }
      function GetTitle    () { return $this->title; }
      function GetField    () { return $this->field; }
      function GetID       () { return $this->id; }
      function GetValue    () { return $this->datatype->GetValue (); }
      function Value       () { return $this->datatype->Value (); }

      function SetDataset  ($v) { $this->dataset = $v; }
      function SetDatatype ($v) { $this->datatype = $v; }
      function SetTitle    ($v) { $this->title = trim ($v); }
      function SetField    ($v) { $this->field = trim ($v); }
      function SetValue    ($v) { $this->datatype->SetValue ($v); }

      function GetImportancy   ()   { return $this->settings['important']; }
      function GetInvisibility ()   { return $this->settings['invisible']; }

      function GetDBFieldType  ()   { return $this->datatype->GetDBFieldType (); }

      function DrawEditorForm ($formname = '', $init = true) {
        $this->datatype->DrawEditorForm ($this->field, $formname, $init);
      }

      function ReceiveValue ($formname = '') {
        $this->datatype->ReceiveValue ($this->field, $formname);
      }

      function BuildQueryValue () {
        return $this->datatype->BuildQueryValue ($this->value);
      }

      function BuildCheckImportancy ($formname = '') {
        return $this->datatype->BuildCheckImportancy ($this->field, $formname);
      }

      function BuildInitScript ($formname = '') {
        return $this->datatype->BuildInitScript ($this->field, $formname);
      }

      function DrawContentSettingsForm ($formname = '') {
        $formname .= ($formname)?('_'):('');
        return $this->datatype->DrawContentSettingsForm ($this->title, $formname.$this->field);
      }

      function ReceiveContentSettings ($formname = '') {
        $formname .= ($formname)?('_'):('');
        return $this->datatype->ReceiveContentSettings  ($this->title, $formname.$this->field);
      }

      function NewContentSpawned ($id = -1) {
        return $this->datatype->NewContentSpawned ($this->field, $id);
      }

      function PerformContentDeletion ($id = -1) {
        $this->datatype->PerformContentDeletion ($this->field, $id);
      }

      function GetDataSettings        ()   { return $this->datatype->GetDataSettings (); }
      function UpdateDataSettings     ($v) { return $this->datatype->UpdateDataSettings ($v); }
      function FreeContent            ()   { $this->datatype->FreeContent (); }
      function FreeValue              ()   { $this->datatype->FreeValue (); }
    }

    function manage_spawn_datafield ($id, $dataset = '', $type = '',
                                     $title = '', $field = '') {
      $c = new CDataField ();
      $c->Init ($id);

      if ($id < 0) {
        $c->SetDataset ($dataset);
        $c->SetDatatype ($type);
        $c->SetTitle ($title);
        $c->SetField ($field);
      }
      return $c;
    }

    function manage_datafield_create ($dataset, $type, $title, $field, $out) {
      $out = manage_spawn_datafield (-1, $dataset, $type, $title, $field);
      return $out->Create ();
    }

    function manage_datafield_delete ($id) {
      $c = manage_spawn_datafield ($id);
      $c->Destroy ();
    }

    function manage_datafield_update ($id, $title, $out) {
      $out = manage_spawn_datafield ($id);
      return $out->Update ($title);
    }

    function manage_datafield_update_received ($id, $out)   {
      $out = manage_spawn_datafield ($id);
      $out->UpdateReceived ();
    }

    function manage_datafield_toggle_importancy ($id) {
      $c = manage_spawn_datafield ($id);
      $c->ToggleImportancy ();
    }

    function manage_datafield_toggle_elem_invisibility ($id) {
      $c = manage_spawn_datafield ($id);
      $c->ToggleInvisibility ();
    }

    function manage_datafield_up   ($id) { $c = manage_spawn_datafield ($id); $c->Up ();}
    function manage_datafield_down ($id) { $c = manage_spawn_datafield ($id); $c->Down ();}
  }
?>
