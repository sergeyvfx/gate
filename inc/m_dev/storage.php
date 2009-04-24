<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Storage of files
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

  if ($_manage_storage_included_ != '#manage_storage_Included#') {
    $_manage_storage_included_ = '#manage_storage_Included#'; 

    $manage_storage_cache = array ();

    class CStorage extends CVirtual  {
      var $name, $path;

      function CStorage () { $this->SetClassName ('CStorage'); }

      function Init ($id, $name = '', $path = '') {
        global $manage_storage_cache;

        $this->id = -1;

        if (isset ($manage_storage_cache[$id])) {
          $this->id = $id;
          $r = $manage_storage_cache[$id];
        } else {
          if ($q = db_select ('storage', array ('*'), "`id`=$id")) {
            $this->id = $id;
            $r = db_row ($q);
          }
        }

        if ($this->id>0) {
          $this->name = $r['name'];
          $this->path = $r['path'];
        }
      }

      function RefCount () { return manage_storage_refcount ($this->id); }
      function Ref      () { return manage_storage_refcount_inc ($this->id); }
      function Unref    () { return manage_storage_refcount_dec ($this->id); }

      function GetFullPath () {
        return config_get ('site-root').config_get ('document-root').
          config_get ('storage-root').$this->path;
      }

      function GetFullFile ($v) {
        if ($v == '') {
          return '';
        }

        $res = $this->GetFullPath ().'/';
        $fn = db_field_value ('storage_volume_'.$this->id, 'file', '`id`='.$v);
        return $res.$fn;
      }

      function GetFullUrl ($v) {
        if ($v == '') {
          return '';
        }

        $res = config_get ('document-root').config_get ('storage-root').
          $this->path.'/';

        $fn = db_field_value ('storage_volume_'.$this->id,
                              'file', '`id`='.$v);

        return $res.$fn;
      }

      function Create () {
        global $_POST;

        $name = addslashes (htmlspecialchars (trim ($this->name)));
        $path = addslashes ($this->path);

        if ($name == '' || $path == '') {
          return false;
        }

        if (!check_dir ($path)) {
          return false;
        }

        if (db_count ('storage', '`name`="'.$name.'"')) {
          add_info ('Хранилище данных с таким именем уже существует.');
          return false;
        } else if (db_count ('storage', '`path`="'.$path.'"')) {
          add_info ('Хранилище данных с таким путем уже существует.');
          return false;
        } else if (dir_exists ($this->GetFullPath ())) {
          add_info ('Данный путь уже используется в системе. Пожалуйста, укажите другой.');
          return false;
        } else {
          $sdir = config_get ('site-root').config_get ('document-root').
            config_get ('storage-root');

          @mkdir ($sdir);
          @chmod ($sdir, 0775);
          @mkdir ($this->GetFullPath ());
          @chmod ($this->GetFullPath (), 0775);

          db_insert ('storage', array ('name'=>"\"$name\"", 'path'=>"\"$path\""));

          $this->id = db_last_insert ();
          db_create_table ('storage_volume_'.$this->id, array (
            'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'file'       => 'TEXT',
            'user_id'    => 'INT',
            'timestamp'  => 'INT',
            'accepted'   => 'BOOL DEFAULT 0',
            'params'     => 'TEXT DEFAULT ""'
          ));
          return true;
        }
      }

      function CreateReceived () {
        $this->SetName (stripslashes ($_POST['name']));
        $this->SetPath ('/'.stripslashes ($_POST['path']));

        if ($this->Create ()) {
          $_POST = array ();
        }
      }

      function Update ($name) {
        $name = addslashes (htmlspecialchars ($name));
        db_update ('storage', array ('name'=>"\"$name\""), '`id`='.$this->id);
        $this->name = $name;
      }

      function UpdateReceived () {
        if ($this->Update (stripslashes ($_POST['name']))) {
          $_POST = array ();
        }
      }

      function Destroy () {
        if ($this->RefCount () > 0) {
          return;
        }

        db_query ('DELETE FROM `storage` WHERE `id`='.$this->id);
        db_destroy_table ('storage_volume_'.$this->id);
        rec_unlink ($this->GetFullPath ());
        manage_storage_unregister ($this->id);
      }

      function GetName () { return $this->name; }
      function GetPath () { return $this->path; }

      function SetName ($v) { $this->name = $v; }
      function SetPath ($v) { $this->path = $v; }

      function GetID () { return $this->id; }

      function GetFileParams ($id) {
        if ($this->id <= 0) {
          return array ();
        }

        $r = db_field_value ('storage_volume_'.$this->id, 'params', "`id`=$id");
        return unserialize ($r);
      }

      function SpawnEntry ($user_id = -1) {
        if ($this->id < 0) {
          return -1;
        }

        db_insert ('storage_volume_'.$this->id, array (
          'file'        => '""',
          'user_id'     => $user_id,
          'timestamp'   => time (),
          'params'      => '""'
        ));

        if (db_error () != '') {
          return -1;
        }

        return db_last_insert ();
      }

      function SpawnFilename ($id, $data) {
        $ext = strtolower (preg_replace ('/.*\./si', '', $data['name']));
        $fn = sprintf ('data_%0'.config_get ('storage-digits').'d.%s', $id, $ext);
        return $fn;
      }

      function MoveUploaded ($data, $file) {
        $full = $this->GetFullPath ().'/'.$file;
        move_uploaded_file ($data['tmp_name'], $full);
        @chmod ($full, 0664);
      }

      function GetImageParams ($data, $arr) {
        $size = GetImageSize ($data['tmp_name']);
        $arr['width']  = $size[0];
        $arr['height'] = $size[1];
        $arr['bits']   = $size['bits'];
      }

      function GetUploadingParams ($data) {
        $arr = array ();
        $arr['mime'] = $data['type'];
        $arr['size'] = $data['size'];
        $arr['ext']  = preg_replace ('/.*\./si', '', $data['name']);

        if (preg_match ('/^image/',$arr['mime'])) {
          $this->GetImageParams ($data, &$arr);
        }

        return $arr;
      }

      function Put ($data, $user_id=-1) {
        $id = $this->SpawnEntry ($user_id);

        if ($id < 0) {
          return;
        }

        $file = $this->SpawnFilename ($id, $data);
        $params = $this->GetUploadingParams ($data);
        $this->MoveUploaded ($data, $file);
        db_update ('storage_volume_'.$this->id, array ('file' => '"'.addslashes ($file).'"',
            'params' => '"'.addslashes (serialize ($params)).'"'), '`id`='.$id);

        return $id;
      }

      function Accept ($id) {
        db_update ('storage_volume_'.$this->id, array ('accepted'=>'1'), "`id`=$id");
      }

      function AcceptFile ($fn) {
        $id = $this->GetFileID ($fn);

        if ($id > 0) {
          $this->Accept ($id);
        }
      }

      function Unlink ($id) {
        $full = $this->GetFullFile ($id);
        @unlink ($full);
        db_delete ('storage_volume_'.$this->id, "`id`=$id");
      }

      function UnlinkFile ($fn) {
        $id=$this->GetFileID ($fn);
        if ($id > 0) {
          $this->Unlink ($id);
        }
      }

      function DeleteUnwanted () {
        $q = db_select ('storage_volume_'.$this->id, array ('id'),
            '`timestamp`<'.(time ()-config_get ('storage-lifetime')).' AND `accepted`=0');

        while ($r = db_row ($q)) {
          $this->Unlink ($r['id']);
        }
      }

      function GetFileID ($fn) {
        return db_field_value ('storage_volume_'.$this->GetID (), 'id', '`file`="'.addslashes ($fn).'"');
      }
    }

    function manage_spawn_storage ($id, $name = '', $path = '') {
      $c = new CStorage ();
      $c->Init ($id, $name, $path);
      return $c;
    }

    function manage_storage_refcount ($id) {
      return db_field_value ('storage', 'refcount', "`id`=$id");
    }

    function manage_storage_refcount_inc ($id) {
      db_update ('storage', array ('refcount' => '`refcount`+1'), "`id`=$id");
    }

    function manage_storage_refcount_dec ($id) {
      db_update ('storage', array ('refcount' => '`refcount`-1'), "`id`=$id");
      print (db_error ());
    }

    function manage_storage_create ($name, $path, $out = null) {
      $out = manage_spawn_storage (-1, $name, $path);
      return $out->Create ();
    }

    function manage_storage_create_received ($out = null) {
      $out = manage_spawn_storage (-1);
      return $out->CreateReceived ();
    }

    function manage_storage_update ($id, $out = null) {
      $out = manage_spawn_storage ($id);
      return $out->UpdateReceived ();
    }

    function manage_storage_delete ($id) {
      $out = manage_spawn_storage ($id);
      return $out->Destroy ();
    }

    function manage_storage_get_list () {
      global $manage_storage_cache;
      $q = db_select ('storage', array ('*'), '', 'ORDER BY `name`');

      while ($r = db_row ($q)) {
        $manage_storage_cache[$r['id']] = $r;
        $arr[] = manage_spawn_storage ($r['id']);
      }

      return $arr;
    }

    function manage_storage_unregister ($id) {
      global $manage_storage_cache;
      unset ($manage_storage_cache[$id]);
    }

    function manage_stroage_exists ($id) {
      if ($id == '') {
        return false;
      }

      return db_count ('storage', '`id`='.$id) > 0;
    }

    function manage_storage_by_dir ($dir) {
      $root_patt = prepare_pattern (config_get ('storage-root'));

      if (!preg_match ('/'.$root_patt.'/', $dir)) {
        return null;
      }

      $dir = preg_replace ('/'.$root_patt.'/', '', $dir);
      $id = db_field_value ('storage', 'id', '`path`="'.addslashes ($dir).'"');

      if ($id > 0) {
        $storage = new CStorage ();
        $storage->Init ($id);
        return $storage;
      }

      return null;
    }
  }
?>
