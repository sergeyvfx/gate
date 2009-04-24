<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Templates managment sybsystem
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

  if ($_manage_tpl_included_ != '#manage_tpl_Included#') {
    $_manage_tpl_included_ = '#manage_tpl_Included#';

    global $manage_templates_arr;
    $manage_templates_arr = 0;
    $manage_template_cache = nil;

    class CTemplate extends CVirtual {
      var $id, $name, $text, $refcount;

      function CTemplate ($id = -1, $name = '', $text = '') {
        $this->SetClassName ('CTemplate');
        $this->Init ($id, $name, $text);
      }

      function Init ($id = -1, $name = '', $text = '') {
        global $manage_template_cache;
        $this->SetDefaultSettings ();
        $this->name = $name;
        $this->text = $text;
        $this->refcount = 0;

        if ($id != -1 && $name == '') {
          // If name is not empty it means that we have already gotten full info from base
          // ( for da optimizing qurries to base )
          if (isset ($manage_template_cache[$id]['text'])) {
            $r = $manage_template_cache[$id];
          } else {
            $r = db_row_value ('templates', "`id`=$id");
            $manage_template_cache[$id]=$r;
          }

          if ($r['id']==$id) {
            $this->name = $r['name'];
            $this->text = $r['text'];
            $this->refcount=$r['refcount'];
            $this->UnserializeSettings ($r['settings']);
          } else {
            $id=-1;
          }
        }
        $this->id=$id;
      }

      function SetName ($v) { $this->name = $v; }
      function GetName () { return $this->name; }

      function SetText ($v) { $this->text = $v; }
      function GetText () { return $this->text; }

      function GetID () { return $this->id; }

      function RefCount () { return $this->refcount; }
      function Ref () {
        if ($this->GetID () < 0) {
          return;
        }

        db_update ('templates', array ('refcount'=>'`refcount`+1'),
                   '`id`='.$this->GetID ());

        $this->refcount++;
      }

      function Check ($silent = false) {
        if (trim ($this->GetName ()) == '') {
          if (!$silent) {
            add_info ('Имя шаблона не может быть пустым.');
            return false;
          }
        }

        if (manage_template_exists ($this->name, $this->id)) {
          if (!$silent) add_info ('Шаблон с таким именем уже существует.'); return false;
        }
        return true;
      }

      function Create () {
        db_insert ('templates', array ('name' => '"'.addslashes ($this->GetName ()).'"',
          'text' => '"'.addslashes ($this->GetText ()).'"'));
        $this->id = db_last_insert ();
        manage_register_template ($this->id, $this->GetName ());
      }

      function Update () {
        manage_template_cache_update_item ($this->id, $this->GetName (),
                                           $this->GetText ());
        db_update ('templates', array ('name' => '"'.addslashes ($this->GetName ()).'"',
          'text' => '"'.addslashes ($this->GetText ()).'"'),
          '`id`='.$this->id);
      }

      function Save () {
        if (!$this->Check (true)) {
          return false;
        }

        if ($this->id == -1) {
          $this->Create ();
        } else {
          $this->Update ();
        }

        return true;
      }

      function Delete () {
        if ($this->RefCount () > 0) {
          return false;
        }

        db_delete ('templates', '`id`='.$this->id);
        manage_unregister_template ($this->id);
        return true;
      }

      function Draw () { println (eval ('?>'.$this->GetText ())); }
    }

    function manage_spawn_template ($id = -1, $name = '', $text = '') {
      $c = new CTemplate ($id, $name, $text);
      return $c;
    }

    function manage_template_exists ($__name, $ignore = -1) {
      global $manage_template_cache;

      // Fill da cache
      if ($manage_template_cache == nil) {
        $manage_template_cache = array ();
        $q = db_select ('templates', array ('id', 'name'));
        while ($r = db_row ($q)) {
          $manage_template_cache[$r['id']] = $r;
        }
      }

      // Search in cache
      foreach ($manage_template_cache as $id => $data) {
        if ($data['name'] == $__name && $id != $ignore) {
          return true;
        }
      }

      return false;
    }

    // Cache updators
    function manage_unregister_template ($id) {
      global $manage_template_cache;
      unset ($manage_template_cache[$id]);
    }

    function manage_template_cache_update_item ($id, $name, $text) {
      global $manage_template_cache;
      $manage_template_cache[$id]['name'] = $name;
      $manage_template_cache[$id]['text']=$text;
    }

    function manage_template_received_create ()  {
      return manage_template_received_update (-1);
    }

    function manage_template_delete ($id) {
      $c = manage_spawn_template ($id);
      $c->Delete ();
    }

    function manage_template_get_list () {
      $arr = array ();
      $q = db_select ('templates', array ('id'), '', 'ORDER BY `name`');

      while ($r = db_row ($q)) {
        $c=new CTemplate ($r['id']);
        $arr[] = $c;
      }

      return $arr;
    }

    function manage_template_received_update ($id) {
      $c = manage_spawn_template ($id);

      if (!$c) {
        $c=new CTemplate ();
      }

      $c->SetName (stripslashes ($_POST['name']));
      $c->SetText (stripslashes ($_POST['text']));

      if ($c->Save ()) {
        $_POST['name'] = $_POST['text'] = '';
      }
    }

    function manage_register_template ($id, $name) {
      global $manage_template_cache;
      $manage_template_cache[$id] = array ('id' => $id,
                                           'name' => $name);
    }

    function manage_template_by_name ($name) {
      global $manage_template_cache;
      $id = -1;

      if ($manage_template_cache != nil) {
        foreach ($manage_template_cache as $_id=>$data) {
          if ($data['name'] == $name) {
            $id = $_id;
            break;
          }
        }
      }

      if ($id < 0) {
        $id = db_field_value ('templates', 'id', '`name`="'.addslashes ($name).'"');
      }

      if ($id == '') {
        $id=-1;
      }

      return manage_spawn_template ($id);
    }

    function manage_template_register_default () {
      global $DOCUMENT_ROOT;
      $d = tpl_dir_relative ().'/front/default';
      manage_template_register_iterator ($d);
    }

    function manage_template_register_iterator ($d) {
      global $DOCUMENT_ROOT;

      $arr = dir_listing ($d);
      $n = count ($arr);

      for ($i = 0; $i < $n; $i++) {
        $fn = $d.'/'.$arr[$i];

        if (!is_file ($DOCUMENT_ROOT.$fn)) {
          manage_template_register_iterator ($fn);
        } else {
          $data = get_file ($DOCUMENT_ROOT.$fn);
          $name = preg_replace ('/^(.*)\n(.*\n)*/', '\1', $data);
          $data = preg_replace ('/^(.*)\n((.*\n)*)/', '\2', $data);
          $c = manage_spawn_template (-1, $name, $data);
          $c->Save ();
          $c->Ref ();
        }
      }
    }

    function manage_template_draw_selector_for_script ($name, $active = -1,
                                                       $cacheable = true) {
      global $manage_templates_arr;

      if (!$cacheable || !$manage_templates_arr) {
        $manage_templates_arr = manage_template_get_list ();
      }

      $n = count ($manage_templates_arr);
      println ('<select name="'.$name.'_script_selector" class="block">');
      println ('  <option value="-1">Не указан</option>');
      for ($i = 0; $i < $n; $i++) {
        println ('  <option value="'.$manage_templates_arr[$i]->GetID ().'"'.
                     (($manage_templates_arr[$i]->GetID ()==$active)?(' selected'):('')).
                     '>'.$manage_templates_arr[$i]->GetName ().'</option>');
      }

      println ('</select>');
    }

    function manage_template_receive_from_selector ($name) {
      $res = $_POST[$name.'_script_selector'];
      if (!isnumber ($res)) {
        $res = -1;
      }
      return $res;
    }

    // Front
    function draw_template ($name, $args = array ()) {
      $tpl = manage_template_by_name ($name);
      tpl_srcp ($tpl->GetText (), $args);
    }
  }
?>
