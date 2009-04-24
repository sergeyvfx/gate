<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Wiki content implementation
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

  if ($_wiki_content_included_ != '#wiki_content_Included#') {
    $_wiki_content_included_ = '#wiki_content_Included#'; 

    class CWikiContent extends CVirtual {
      var $name;
      var $path;
      var $security;
      var $id, $pId;
      var $data;
      var $realSecurity;

      function CWikiContent () { $this->SetClassName ('CWikiContent'); }

      function GetFullPath ($ovewritePath = '') {
        $ovewritePath = preg_replace ('/^\//', '', $ovewritePath);
        $prefix = config_get ('site-root').config_get ('document-root');
        if ($this->id > 1) {
          $cp = content_path ($this->pId);

          if ($cp != '') {
            $prefix .= $cp;
          }
        }

        $prefix .= '/';

        if ($ovewritePath == '') {
          return $prefix.$this->path;
        }

        return $prefix.$ovewritePath;
      }

      function GetFullHTTPPath () {
        return content_path ($this->GetID ());
      }

      function UpdateRealSecurity ($force = false) {
        if ($this->realSecurity && !$force) {
          return;
        }

        $this->realSecurity = new CSecurityInformation ();
        $this->realSecurity->Init ('security', $this->security->GetData ());
        $pid = $this->pId;

        while ($this->realSecurity->GetInherit ()) {
          if ($pid == '') {
            break;
          }

          $r = db_row_value ('content', '`id`='.$pid);
          $arr = unserialize ($r['settings']);
          $this->realSecurity->SetData ($arr['security']);

          if ($pid == 1) {
            break;
          }

          $pid = $r['pid'];
        }
      }

      function GetRealSecurity () {
        $this->UpdateRealSecurity ();
        return $this->realSecurity;
      }
    
      function Init ($id) {
        $this->security = new CSecurityInformation ();
        $this->security->Init ('security');
        $q = db_select ('content', array ('*'), '`id`='.$id);

        if (db_affected () > 0) {
          $this->id = $id;
        } else {
          $this->id = -1;
        }

        if ($this->id > 0) {
          $r = db_row ($q);
          $this->pId  = $r['pid'];
          $this->name = $r['name'];
          $this->path = $r['path'];
          $this->UnserializeSettings ($r['settings']);
          $this->security->SetData ($this->settings['security']);
          $this->UpdateRealSecurity ();

          if ($r['class']) {
            $this->data = new $r['class'] ();
            $this->data->UpdateSettings ($this->settings['data']);
            $this->data->Init ($id, $this->realSecurity);
          }

        } else {
          $this->data = new CCvirtual ();
          $this->data->Init (-1, $this->realSecurity);
          $this->security->SetDefaultData ();
        }
        if ($this->id == 1) {
          $this->security->SetCanInherit (false);
        }

        if ($this->data) {
          $this->data->SetName ($this->GetName ());
        }
      }

      function CreateSiteDir () {
        create_dir ($this->GetFullPath ());
        $this->data->Editor_CreateDirContent ($this->GetFullPath ());
      }

      function RenameSiteDir ($old, $new) {
        if ($this->id <= 1) {
          return;
        }

        rename ($old, $new);
      }

      function RemoveSiteDir () {
        if ($this->id == -1) {
          return;
        }

        $this->data->Editor_DeleteDirContent ($this->GetFullPath ());
        rec_unlink ($this->GetFullPath ());
      }

      function MoveSiteDir ($newPath) {
        if (!$this->GetAllowed ('EDIT')) {
          return false;
        }

        $oldPath = $this->GetFullPath ();
        $path = config_get ('site-root').config_get ('document-root').$newPath;

        if ($oldPath == $path) {
          return false;
        }

        if (dir_exists ($this->path)) {
          return false;
        }

        create_dir ($path);
        $this->data->Editor_MoveDirContent ($oldPath, $path);
        rec_unlink ($oldPath);

        return true;
      }

      function SetParent ($pid) {
        if (!$this->GetAllowed ('EDIT')) {
          return false;
        }

        if ($this->id == -1 || $this->id == 1) {
          return false;
        }

        if ($this->pId == $pid) {
          return false;
        }

        if (!wiki_content_exists ($pid)) {
          add_info ('Нельзя перемещать раздел в несуществующий.');
          return false;
        }

        if (wiki_content_in_node ($this->id, $pid)) {
          add_info ('Извольте, да разве можно перемещать раздел в его потомка?');
          return false;
        }

        if (wiki_content_present_in_node ($pid, $this->path)) {
          add_info ('Раздел с аналогичным названием виртуальной '.
                    'папки уже содержится в этом узле.');
          return false;
        }

        $p = wiki_spawn_content ($pid);

        if (!dir_exists ($p->GetFullPath ())) {
          add_info ('Каталог целевого раздела не существует. '.
                    'Для предотвращения потери информации перемещение '.
                    'не будет произведено.');
          return false;
        }

        $this->pid = $pid;
        $parentPath = content_path ($pid);
        $newPath = $parentPath.'/'.$this->path;

        if ($this->MoveSiteDir ($newPath)) {
          $this->pid = $pid;
          $order = db_next_order ('content', "`pid`=$pid");
          db_update ('content', array ('pid' => $pid, 'order' => $order),
                     '`id`='.$this->id);
          return true;
        }

        return false;
      }

      function Create () {
        $name = $this->name;
        $path = $this->path;
        $dataset = $this->datasetId;
        $cclass = $this->cclass;

        if (wiki_content_present_in_root ($path)) {
          add_info ('Раздел с таким названием виртуальной папки '.
                    'уже присутсвыет в корне сайта.');
          return false;
        }

        $name = addslashes (htmlspecialchars (trim ($name)));
        if ($name == '') {
          return false;
        }

        if ($this->id != 1) {
          if (!check_folder ($path)) {
            add_info ('Название виртуальной папки может состоять '.
                      'только из букв латинского алфавита и цифр');
            return false;
          }
        } else {
          $path = $this->path = '/';
        }

        $order = db_next_order ('content', '`pid`=1');
        $this->ReceiveSettings ();
        $this->settings['security'] = $this->security->GetData ();
        db_insert ('content', array ('name' => "\"$name\"",
                                     'path' => "\"$path\"",
                                     'order' => "\"$order\"",
                                     'class' => "\"$cclass\""));
        $this->id = db_last_insert ();
        $this->data = new $cclass ();
        $this->data->Init ($this->id, $this->reasSecurity);

        if (!$this->data->ReceiveSettings ('settings_form_'.$cclass)) {
          db_delete ('content', '`id`='.$this->id);
          return false;
        }

        $this->settings['data'] = $this->data->GetSettings ();
        db_update ('content', array ('settings' =>
                                       '"'.
                                       addslashes ($this->SerializeSettings ()).
                                       '"'),
                   '`id`='.$this->id);
        $this->CreateSiteDir ();
        $this->UpdateRealSecurity ();

        return true;
      }
    
      function Update () {
        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        $oldpath = $this->oldPath;
        $name = $this->name;
        $path = $this->path;

        if (wiki_content_present_in_node ($this->pId, $path, $this->id)) {
          add_info ('Раздел с таким названием виртуальной папки '.
                    'уже присутсвыет в данной ветке структуры сайта.');
          return false;
        }

        $n = $name;
        $p = $path;
        $name = htmlspecialchars (addslashes (trim ($name)));
        if ($name == '') {
          return false;
        }

        if ($this->id != 1) {
          if (!check_folder ($path)) {
            add_info ('Название виртуальной папки может состоять только '.
                      'из букв латинского алфавита и цифр');
            return false;
          }
        } else  {
          $path = $this->path = '/';
        }

        $this->settings['security'] = $this->security->GetData ();
        db_update ('content', array ('name' => "\"$name\"",
                                     'path' => "\"$path\"",
                                     'settings'=>'"'.addslashes (
                                           $this->SerializeSettings ()).'"'),
                   '`id`='.$this->id);

        if ($oldpath != $path) {
          $p = config_get ('site-root').config_get ('document-root').'/';
          $this->RenameSiteDir ($p.$oldpath, $p.$path);
        }

        $this->name = $n;
        $this->path = $p;
        $this->UpdateRealSecurity ();

        if ($this->data) {
          $this->data->SetName ($this->GetName ());
        }

        return true;
      }
    
      function CreateReceived () {
        $this->SetName (stripslashes ($_POST['name']));
        $this->SetPath (stripslashes ($_POST['path']));
        $this->cclass = $_POST['class'];

        $r = $this->Create ();

        if ($r) {
          $_POST = array ();
        }

        return $r;
      }

      function UpdateReceived () {
        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        $this->oldPath = $this->path;
        $this->SetName (stripslashes (trim ($_POST['name'])));
        $this->SetPath (stripslashes (trim ($_POST['path'])));
        $this->security->ReceiveData ();
        $r = $this->Update ($oldpath);

        if ($r) {
          $_POST = array ();
        }

        return $r;
      }
    
      function Destroy () {
        if (!$this->GetAllowed ('DELETE')) {
          return;
        }

        $arr = arr_from_query ('SELECT `id` FROM `content` WHERE `pid`='.
                               $this->id);

        for ($i = 0; $i < count ($arr); $i++) {
          $c = wiki_spawn_content ($arr[$i]['id']);
          $c->Destroy ();
        }

        // TODO:
        // Add da unref stuff here

        $this->data->PerformDeletion ();
        $this->RemoveSiteDir ();

        db_delete ('content', '`id`='.$this->id);
      }

      function GetID   () { return $this->id; }
      function GetPID  () { return $this->pId; }
      function GetName () { return $this->name; }
      function GetPath () { return $this->path; }
      function GetSecurityInformation () { return $this->security; }
    
      function SetName ($v) { $this->name = $v; }
      function SetPath ($v) { $this->path = $v; }
    
      function Up () {
        db_move_up ('content', $this->id, '`pid`='.$this->pId);
      }
      function Down () {
        db_move_down ('content', $this->id, '`pid`='.$this->pId);
      }
    
      //////
      // Content creating&editing stuff
      // Draw the settings form
      function DrawSettingsForm ($form_name) {  }

      // Receives the settngs from the creation form
      function ReceiveSettings  () {  }
      function PerformDeletion  () {  }

      function Editor_ManageEditForm   () {
        content_url_var_push_global ('action');
        content_url_var_push_global ('function');
        content_url_var_push_global ('id');
        $f = editor_get_function ();

        if ($f != '') {
          $this->data->$f ();
        }
      }

      function GetAllowedToUser ($uid, $action) {
        if (user_access_root ()) {
          return true;
        }

        return $this->realSecurity->GetAllowedToUser ($uid, $action);
      }

      function GetAllowed ($action) {
        if (user_access_root ()) {
          return true;
        }

        return $this->GetAllowedToUser (user_id (), $action);
      }
    
      // Links to data
      function Editor_DrawContent ($vars = array ()) {
        if ($this->GetAllowed ('READ')) {
          $this->data->Editor_DrawContent ($vars);
        }
      }

      function Editor_EditForm () {
        if ($this->GetAllowed ('EDIT')) {
          $this->data->Editor_EditForm ('content_'.$this->id);
        }
      }

      function Editor_DrawHistory () {
        if ($this->GetAllowed ('EDIT')) {
          $this->data->Editor_DrawHistory ();
        }
      }

      function GetData () { return $this->data; }
      function GetRSSData ($limit) { return $this->data->GetRSSData ($limit); }
  }

    function wiki_spawn_content ($id = -1, $cclass = 'CCVirtual',
                                 $name = '', $path = '', $virtual = false) {
      $c = new CWikiContent ();
      $c->Init ($id);
      return $c;
    }

    function wiki_content_create ($name, $path,
                                  $security = null, $out = null) {
      $out = wiki_spawn_content (-1);
      $out->Create ($name, $path, $security);
    }

    function wiki_content_create_received ($out = null) {
      $out = wiki_spawn_content (-1, $_POST['class']);
      $out->CreateReceived ();
    }

    function wiki_content_update ($id, $name, $path,
                                  $security = null, $out = null) {
      $out = wiki_spawn_content ($id);
      $out->Create ($name, $path, $security);
    }

    function wiki_content_update_received ($id, $out = null) {
      $out = wiki_spawn_content ($id);
      $out->UpdateReceived ();
    }

    function wiki_content_delete ($id) {
      $c = wiki_spawn_content ($id);
      $c->Destroy ();
    }

    function wiki_content_present_in_node ($id, $path, $skipId = -1) {
      $path = addslashes ($path);
      $count = db_count ('content', "`pid`=\"$id\" AND `path`=\"$path\" ".
                         "AND `id`<>$skipId");

      if ($count > 0) {
        return true;
      }

      $npath = content_path ($id);

      if ($path != '/') {
        $p = $npath.'/'.$path;
      } else {
        $p = '/';
      }

      $cid = content_id_by_path ($p);

      if ($cid > 0 && $cid == $skipId) {
        return false;
      }

      return dir_exists (config_get ('site-root').
                         config_get ('document-root').$p);
    }

    function wiki_content_present_in_root ($path, $skipId = -1) {
      return wiki_content_present_in_node (1, $path, $skipId );
    }

    function wiki_content_get_list () {
      $arr = array ();
      $q = db_query ('SELECT * FROM `content` ORDER BY `order`');
      while ($r = db_row ($q)) {
        $r['settings'] = unserialize ($r['settings']);
        $r['security'] = unserialize ($r['security']);
        $arr[] = $r;
      }
      return $arr;
    }
  
    function wiki_content_up ($id) {
      $c = wiki_spawn_content ($id);
      $c->Up ();
    }
    function wiki_content_down ($id) {
      $c = wiki_spawn_content ($id);
      $c->Down ();
    }
  
    function wiki_content_rec ($id, $res) {
      if ($id <= 1) {
        return '';
      }

      $r = db_row_value ('content', "`id`=$id");
      wiki_content_rec ($r['pid'], &$res);
      $res[] = $r;
    }

    function wiki_content_navigator ($id, $getdata='') {
      $arr = array ();
      wiki_content_rec ($id, &$arr);

      for ($i = 0; $i < count ($arr) - 1; $i++) {
        $res .= '<a href=".?'.$getdata.(($getdata!='')?('&'):('')).'id='.
          $arr[$i]['id'].'">'.$arr[$i]['name'].'</a>';
      }
      $res .= $arr[count ($arr) - 1]['name'];
      return $res;
    }

    function wiki_content_exists ($id) {
      return db_count ('content', "`id`=$id") > 0;
    }
  
    function wiki_content_in_node ($nid, $cid) {
      $r = db_row_value ('content', "`id`=$cid");
      for (;;) {
        if ($r['id'] == '1') {
          break;
        }

        if ($r['id'] == $nid) {
          return true;
        }

        $r = db_row_value ('content', '`id`='.$r['pid']);
      }

      return false;
    }
  
    function wiki_content_set_parent ($src, $dst) {
      if (!wiki_content_exists ($src)) {
        return false;
      }

      if (!wiki_content_exists ($dst)) {
        return false;
      }

      $c = wiki_spawn_content ($src);
      $c->SetParent ($dst);
    }
  }
?>