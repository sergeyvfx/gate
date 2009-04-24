<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Datatype for Wiki pages
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

  if ($_manage_dt_included_ != '#manage_dt_Included#') {
    $_manage_dt_included_ = '#manage_dt_Included#'; 

    class CDataType extends CVirtual {
      var $name, $data;

      function RefCount () { return manage_datatype_refcount ($this->id); }
      function Ref      () { return manage_datatype_refcount_inc ($this->id); }
      function Unref    () { return manage_datatype_refcount_dec ($this->id); }

      function CDataType () { $this->SetClassName ('CDataType'); }

      function SpawnData ($class, $settings = array ()) {
        $this->data = new $class;
        $this->data->Init ();
        $this->data->UpdateSettings ($settings);
      }

      function Init ($id) {
        if (($q=db_select ('datatypes', array ('*'), "`id`=$id")) &&
            db_affected ()>0) {
          $this->id = $id;
        } else {
          $this->id = -1;
        }

        if ($this->id > 0) {
          $r = db_row ($q);
          $this->name = $r['name'];
          $this->UnserializeSettings ($r['settings']);
          $this->data = new $r['class'];
          $this->data->Init ();
          $this->data->UnSerializeSettings ($this->settings['data']);
        }
      }

      function Create () {
        $name = addslashes (htmlspecialchars ($this->name));
        $settings = addslashes ($this->SerializeSettings ());

        if ($name == '') {
          return;
        }

        if (db_count ('datatypes', '`name`="'.$name.'"') <= 0) {
          db_insert ('datatypes', array ('name'=>"\"$name\"", 'class'=>'"'.
            addslashes ($this->data->GetClassName ()).'"',
            'settings'=>"\"$settings\"", 'refcount'=>0));

          return true;
        } else {
          add_info ('Тип данных с таким именем уже существует.');
          return false;
        }
      }

      function CreateReceived () {
        $dataClass = $_POST['dcName'];
        $this->SpawnData ($dataClass);
        $this->data->ReceiveSettings ();
        $this->settings['data'] = $this->data->SerializeSettings ();
        $this->SetName (stripslashes ($_POST['className']));

        if ($this->Create ()) {
          $_POST = array ();
        }
      }

      function UpdateReceived () {
        $name = htmlspecialchars (trim ($_POST['className']));
        if ($name == '') {
          return;
        }

        if (db_count ('datatypes', '`name`="'.$name.'" AND `id`<>'.$this->id) > 0) {
          add_info ('Тип данных с таким именем уже существует.');
        } else {
          db_update ('datatypes', array ('name'=>"\"$name\""), '`id`='.$this->id);
          $this->name=stripslashes (trim ($_POST['className']));
          $_POST=array ();
        }
      }

      function Destroy () {
        if (manage_datatype_refcount ($this->id)>0) {
          return;
        }

        db_query ('DELETE FROM `datatypes` WHERE `id`='.$this->id);
      }

      function SetName      ($v)  { $this->name=trim ($v); }
      function GetName      () { return $this->name; }

      function GetDataClass () { return $this->data->GetClassName (); }

      function DrawEditorForm     ($field, $formname = '', $init = true) {
        $this->data->DrawEditorForm ($field, $formname, $init);
      }

      function ReceiveValue ($field, $formname = '') {
        $this->data->ReceiveValue ($field, $formname);
      }

      function BuildQueryValue () { return $this->data->BuildQueryValue (); }

      function BuildCheckImportancy ($field, $formname = '') {
        return $this->data->BuildCheckImportancy ($field, $formname);
      }

      function GetDBFieldType () {
        return $this->data->GetDBFieldType ();
      }

      function BuildInitScript ($field, $formname = '') {
        return $this->data->BuildInitScript ($field, $formname);
      }

      function DrawContentSettingsForm ($title, $field) {
        return $this->data->DrawContentSettingsForm ($title, $field);
      }

      function ReceiveContentSettings  ($title, $field) {
        if (!$this->data->ReceiveContentSettings ($title, $field)) {
          return false;
        }

        $this->settings['data'] = $this->data->SerializeSettings ();

        return true;
      }

      function NewContentSpawned      ($field, $content_id = -1) {
        return $this->data->NewContentSpawned ($field, $content_id);
      }

      function PerformContentDeletion ($field, $content_id) {
        return $this->data->PerformContentDeletion ($field, $content_id);
      }

      function SetValue ($v) { $this->data->SetValue ($v); }
      function GetValue ()   { return $this->data->GetValue (); }
      function Value ()      { return $this->data->Value (); }

      function GetDataSettings    () {
        $a = $this->data->GetSettings ();
        return $a['data'];
      }

      function UpdateDataSettings ($v) {
        return $this->data->UpdateSettings ($v);
      }

      function FreeContent () { $this->data->DestroyValue (); }
      function FreeValue ()   { $this->data->FreeValue (); }
    }

    function manage_spawn_datatype ($id, $name = '', $baseClass = '',
                                   $settings = array ()) {
      $c = new CDataType ();
      $c->Init ($id);
      if ($id<0) {
        $c->SetName ($name);
        $c->SetSettings ($settings);
      }
      return $c;
    }

    function manage_datatype_refcount ($id) {
      return db_field_value ('datatypes', 'refcount', "`id`=$id");
    }

    function manage_datatype_refcount_inc ($id) { db_update ('datatypes', array ('refcount'=>'`refcount`+1'), "`id`=$id"); }
    function manage_datatype_refcount_dec ($id) { db_update ('datatypes', array ('refcount'=>'`refcount`-1'), "`id`=$id"); }

    function manage_datatype_create ($name, $baseClass, $settings) {
      $c = manage_spawn_datatype (-1, $name, $baseClass, $settings);
      return $c->Create ();
    }

    function manage_datatype_received_create () {
      $c = manage_spawn_datatype (-1);
      return $c->CreateReceived ();
    }

    function manage_datatype_delete ($id) {
      $c = manage_spawn_datatype ($id);
      $c->Destroy ();
    }

    function manage_datatype_update_received ($id) {
      $c = manage_spawn_datatype ($id);
      $c->UpdateReceived ();
    }

    /* TOTO: !!! PATCJH ME !!! */
    function manage_datatype_getlist () {
      $q = db_query ('SELECT * FROM `datatypes` ORDER BY `name`');
      $arr = array ();

      while ($r = db_row ($q)) {
        $arr[]=$r;
      }

      return $arr;
    }

    function manage_datatype_get_by_name ($name) {
      $classes = content_Registered_DCClasses ();

      for ($i=0; $i<count ($classes); $i++) {
        if ($classes[$i]['class'] == $name) {
          return $classes[$i];
        }
      }

      return array ();
    }
  }
?>
