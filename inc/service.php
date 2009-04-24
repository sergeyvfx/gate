<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Service managing class
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

  if ($_service_Included_ != '#_service_Included') {
    $_service_Included_ = '#_service_Included';

    $services = array ();
    $services_link = array ();

    class CService extends CVirtual {
      var $service, $name, $className;

      function SpawnService () {
        $this->service = new $this->className;
        $this->service->Init ();
        $this->service->InitInstance ($this->id, $this->_virtual);
      }

      function Init ($id = -1, $class = '', $virtual = false) {
        $this->service = null;
        $this->_virtual = $virtual;

        if ($q = db_select ('service', array ('*'), "`id`=$id")) {
          $this->id = $id;
        } else {
          $this->id = -1;
        }

        if ($id == -1) {
          $this->SetClassName ($class);
          $this->SpawnService ();
        } else {
          $r = db_row ($q);
          $this->SetClassName ($r['sclass']);
          $this->SetName ($r['name']);
          $this->SpawnService ();
          $this->service->UnserializeSettings ($r['settings']);
        }
        $this->id = $id;
      }

      function Create () {
        $name = htmlspecialchars (addslashes ($this->name));
        $class = htmlspecialchars (addslashes ($this->className));

        $this->SpawnService ();
        $this->service->Create ();

        if (!$this->service->CanCreate ()) {
          return false;
        }

        if (db_count ('service', '`name`="'.$name.'"') > 0) {
          add_info ('Сервис с таким именем уже существует.');
          return false;
        }

        $this->service->ReceiveSettings ();
        $settings = addslashes ($this->service->SerializeSettings ());
        db_insert ('service', array ('name' => "\"$name\"", 'sclass' => "\"$class\"",
            'settings' => "\"$settings\""));

        return true;
      }

      function Update () {
        $name = htmlspecialchars (addslashes ($this->name));

        if (db_count ('service', '`name`="'.$name.'" AND `id`<>'.$this->id) > 0) {
          add_info ('Сервис с таким именем уже существует.');
          return false;
        }

        db_update ('service', array ('name' => "\"$name\""), '`id`='.$this->id);
        return true;
      }

      function Destroy () {
        $this->service->PerformDeletion ();
        if ($this->id>0) {
          db_delete ('service', '`id`='.$this->id);
        }
      }

      function CreateReceived () {
        $this->SetName (stripslashes ($_POST['name']));
        $this->SetClassName (stripslashes ($_POST['service']));
        $this->SpawnService ();
        $this->service->ReceiveSettings ();

        if ($this->Create ()) {
          $_POST = array ();
        } else {
          return false;
        }

        return true;
      }

      function UpdateReceived () {
        $this->SetName (stripslashes ($_POST['name']));

        if ($this->Update ()) {
          $_POST = array ();
        }
      }

      function DrawSettingsForm () {
        if ($this->service) {
          $this->service->DrawSettingsForm ();
        }
      }

      function Editor_ManageEditForm () {
        content_url_var_push_global ('action');
        content_url_var_push_global ('function');
        content_url_var_push_global ('id');
        $f = editor_get_function ();

        if ($f != '') {
          $this->service->$f ();
        }
      }

      function SetName ($v) { $this->name = $v; }
      function GetName ()   { return $this->name; }
      function SetClassName ($v) { $this->className = $v; }
      function GetClassName ()   { return $this->className; }

      function GetServiceName () {
        if ($this->service) {
          return $this->service->GetServiceName ();
        }
      }

      function GetService () { return $this->service; }
    }

    function manage_spawn_service ($id = -1, $class = '', $virtual = false) {
      $c = new CService ();
      $c->Init ($id, $class, $virtual);
      return $c;
    }

    function service_checktables () {
      if (!db_table_exists ('service')) {
        db_create_table_safe ('service', array (
          'id'               => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'name'             => 'TEXT',
          'sclass'           => 'TEXT',
          'settings'         => 'TEXT DEFAULT ""',
        ));
      }
    }

    function service_append ($c) {
      global $services, $services_link;
      $i = count ($services);
      $services[$i] = $c;
      $services_link[$c->id] = &$services[$i];
    }

    function service_load () {
      $q = db_select ('service', array ('id'));

      while ($r = db_row ($q)) {
        $c = manage_spawn_service  ($r['id'], '', true);
        service_append ($c);
      }
    }

    function service_initialize () {
      if (config_get ('check-database')) {
        service_checktables ();
      }

      service_load ();
    }

    function service_by_id ($id) {
      global $services_link;
      return $services_link[$id];
    }

    function service_unset_by_id ($id) {
      global $services, $services_link;
      $services_link[$id] = null;

      unset ($services_link[$id]);
    }

    function service_by_classname ($cname) {
      global $services;
      $arr = array ();

      for ($i = 0; $i < count ($services); $i++) {
        if ($services[$i]->GetClassName  () == $cname) {
          $arr[] = &$services[$i];
        }
      }

      return $arr;
    }

    function manage_service_create () {
      $c = manage_spawn_service (-1, $_POST['service']);
      $c->CreateReceived ();
      service_append ($c);
    }

    function manage_service_update_received ($id) {
      $c = service_by_id ($id);
      $c->UpdateReceived ();
    }

    function manage_service_delete ($id) {
      $c = service_by_id ($id);
      if ($c) {
        $c->Destroy ();
        service_unset_by_id ($id);
      }
    }

    function manage_service_get_list () {
      return arr_from_query ('SELECT * FROM `service` ORDER BY `name`');
    }
  }
?>
