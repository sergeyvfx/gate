<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Content manipulating stuff
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

  if ($_content_included_ != '#content_Included#') {
    $_content_included_ = '#content_Included#'; 
    $content_RegisteredClasses = array ();
    $content_url_vars = array ();
    $content_type = '';
    $content_security = null;

    // Initialization of content stuff
    function content_initialize () {
      if (config_get ('check-database')) {
        db_create_table_safe ('content_support_tables', array (
          'id'               => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'content_id'       => 'INT',
          'dataset_id'       => 'INT',
          'unique'           => 'INT DEFAULT -1'
        ));
      }
    }

    function content_RegisterClass ($group, $className, $classPseudonym = '') {
      global $content_RegisteredClasses;
      $content_RegisteredClasses[$group][]=array ('class' => $className,
                                                  'pseudonym' => $classPseudonym);
    }

    function content_Register_VCClass ($className, $classPseudonym='') {content_RegisterClass ('CVC', $className, $classPseudonym);}
    function content_Register_CClass  ($className, $classPseudonym='') {content_RegisterClass ('CC',  $className, $classPseudonym);}
    function content_Register_MCClass ($className, $classPseudonym='') {content_RegisterClass ('CMC', $className, $classPseudonym);}
    function content_Register_DCClass ($className, $classPseudonym='') {content_RegisterClass ('CDC', $className, $classPseudonym);}
    function content_Register_SCClass ($className, $classPseudonym='') {content_RegisterClass ('SC', $className, $classPseudonym);}

    function content_Registered_Classes ($group) {
      global $content_RegisteredClasses;
      return $content_RegisteredClasses[$group];
    }

    function content_Registered_DCClasses () {return content_Registered_Classes ('CDC');}
    function content_Registered_CClasses  () {return content_Registered_Classes ('CC');}
    function content_Registered_SClasses  () {return content_Registered_Classes ('SC');}

    function content_support_table_name ($id, $dataset_id, $uid =- 1) {
      return 'content_support_'.$id.'_'.$dataset_id.(($uid!=-1)?("_$uid"):(''));
    }

    function content_create_support_table ($id, $dataset_id, $fields = array (), $uid = -1) {
      $tname = 'content_support_'.$id.'_'.$dataset_id.(($uid!=-1)?("_$uid"):(''));

      if (db_count ('content_support_tables', "`content_id`=$id AND `dataset_id`=$dataset_id AND `unique`=$uid") > 0) {
        return $tname;
      }

      $f=array ('id'=>'INT NOT NULL PRIMARY KEY AUTO_INCREMENT', 'timestamp'=>'INT', 'user_id'=>'INT',
        'ip'=>'TEXT');

      foreach ($fields as $k=>$v) {
        $f[$k] = $v;
      }

      db_create_table ($tname, $f);
      db_insert ('content_support_tables', array ('content_id' => $id, 'dataset_id' => $dataset_id,
        'unique' => $uid));
      return $tname;
    }

    function content_destroy_support_table ($id, $dataset_id, $uid = -1) {
      if (db_count ('content_support_tables',
                    "`content_id`=$id AND `dataset_id`=$dataset_id AND `unique`=$uid") == 0) {
        return;
      }

      $tname = 'content_support_'.$id.'_'.$dataset_id.(($uid!=-1)?("_$uid"):(''));
      db_destroy_table ($tname);
      db_delete ('content_support_tables', "`content_id`=$id AND `dataset_id`=$dataset_id AND `unique`=$uid");
    }

    function content_id_by_path ($path) {
      $parent = dirname ($path);

      if ($path == '') {
        return -1;
      }

      if ($path == '/') {
        return 1;
      }

      $pid = content_id_by_path ($parent);
      if ($pid == '') {
        return -1;
      }

      $p = basename ($path);
      $r = db_field_value ('content', 'id', "`pid`=$pid AND `path`=\"$p\"");

      if ($r == '') {
        return -1;
      }

      return $r;
    }

    function content_lookup ($path) {
      global $oldid, $content_security;
      $root = config_get ('document-root');
      $pattern = prepare_pattern ($root);
      $path = preg_replace ("/($pattern)/", '', $path);
      $id = content_id_by_path ($path);

      if ($id == -1) {
        return null;
      }

      $c = wiki_spawn_content ($id);
      $content_security = $c->GetRealSecurity ();

      return $c;
    }

    function content_path ($id) {
      if ($id <= 1) {
        return '';
      }

      $r = db_row_value ('content', "`id`=$id");
      return content_path ($r['pid']).'/'.$r['path'];
    }

    function content_get_up_to_root ($path) {
      $root_dir = config_get ('site-root').config_get ('document-root');
      $delta = char_count ('/', $path)-char_count ('/', $root_dir);
      $r = '';

      for ($i = 0; $i < $delta; $i++) {
        $r .= '../';
      }

      return $r;
    }

    function content_error_page ($err, $p = array ()) {
      return tpl ("back/errors/$err", $p);
    }

    // Staic page's content
    function content_static_page ($fname) {
      $fname = config_get ('site-root').$fname.'/'.config_get ('data-file');

      if (($src = get_file ($fname)) == false) {
        $src = content_error_page ('404', array ('url' => $fname));
      }

      return stencil_core_page ($src);
    }

    // Wiki pages
    function content_wiki_page ($url) {
      return wiki_get_page ($url);
    }

    function content_url_var_pop  ($var)         { global $content_url_vars; $res=$content_url_vars[$var]; unset ($content_url_vars[$var]); return $res; }
    function content_url_var_push ($var, $val = '')  { global $content_url_vars; $content_url_vars[$var]=$val; }
    function content_url_var_push_global ($var) { global $content_url_vars; $content_url_vars[$var]=$GLOBALS[$var]; }
    function content_url_get      ($val)        { global $content_url_vars; return $content_url_vars[$var]; }

    function content_url_get_full () {
      global $content_url_vars;
      $php_self = $_SERVER['PHP_SELF'];
      $php_self = preg_replace ('/index.php$/', '', $php_self).'?';
      $printed = false;

      foreach ($content_url_vars as $k=>$v) {
        if ($v != '') {
          if ($printed) {
            $php_self.='&';
          }

          $php_self .= $k.'='.urlencode ($v);
          $printed = true;
        }
      }
      return $php_self;
    }

    function content_get_allowed ($action) {
      global $content_security;

      if ($content_security) {
        return $content_security->GetAllowed ($action);
      }

      return false;
    }

    function content_security_set () {
      global $content_security;
      return $content_security != null;
    }

    function content_recursive_move ($src, $dst) {
      $dir = opendir ($src);
      $oldUp = content_get_up_to_root ($src).'globals.php';
      $newUp = content_get_up_to_root ($dst).'globals.php';

      if (!file_exists ($dst)) {
        mkdir ($dst);
        chmod ($dst, 0775);
      }

      while (($file = readdir ($dir)) != false)
        if ($file != '..' && $file != '.') {
          if (is_dir ($src."/$file")) {
            content_recursive_move ($src."/$file", $dst."/$file");
          } else {
            $data=get_file ($src."/$file");

            if ($file == 'index.php') {
              $data = preg_replace ('/\''.prepare_pattern ($oldUp).'\'/', "'$newUp'", $data);
            }

            create_file ($dst."/$file", $data);
          }
        }
    }

    function content_unavaliable () {
      add_info ('Этот раздел в данное время Вам недоступен.');
    }
  }
?>
