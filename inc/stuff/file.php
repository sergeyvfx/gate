<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Files' manipulation stuff
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

  if ($_files_included_ != '#files_Included#') {
    $_files_included_ = '#files_Included#';

    $file_encrypted_allowed = array ();
    session_register ('file_encrypted_allowed');

    function file_check_tables () {
      if (config_get ('check-database')) {
        if (!db_table_exists ('files')) {
          db_create_table_safe ('files', array (
                                  'id'               => 'INT NOT NULL '.
                                          'PRIMARY KEY AUTO_INCREMENT',
                                  'name'             => 'TEXT',
                                  'orig_name'        => 'TEXT',
                                  'access'           => 'INT',
                                  'blocked'          => 'BOOL'
                                                ));
        }
      }
    }

    function get_file ($file) {
      @ $f=fopen ($file, 'rb');

      if (!$f) {
        return false;
      }

      fseek ($f, 0, SEEK_END);
      $size = ftell ($f);
      fseek ($f, 0, SEEK_SET);

      if ($size > 0) {
        $data=fread ($f, $size);
      }

      fclose ($f);
      return $data;
    }

    function rec_unlink ($path) {
      @$dir = opendir ($path);

      if ($dir) {
        while (($file = readdir ($dir))!=false)
          if ($file != '.' && $file != '..') {
            rec_unlink ($path.'/'.$file);
          }
      }

      @closedir ($dir);
      @unlink ($path);
      @rmdir ($path);
    }

    function dir_exists ($n) {
      $dir = 0;
      @$dir = opendir ($n);

      if ($dir) {
        $r = true;
      } else {
        $r = false;
      }

      @closedir ($dir);
      return $r;
    }

    function create_file ($fn, $data='') {
      @$f = fopen ($fn, 'w');
      chmod ($fn, 0664);

      if (!$f) {
        return;
      }

      fwrite ($f, $data);
      fclose ($f);
    }

    function file_writeblock ($fn, $data = '') {
      create_file ($fn, $data);
    }

    function create_dir ($d) {
      mkdir ($d);
      chmod ($d, 0775);
    }

    function dir_listing ($path) {
      global $DOCUMENT_ROOT;

      $dir = opendir ($DOCUMENT_ROOT.$path);
      $arr = array ();

      while (($file = readdir ($dir)) != false) {
        if ($file != '.' && $file != '..') {
          $arr[] = $file;
        }
      }

      array_multisort ($arr, SORT_ASC, SORT_STRING);
      return $arr;
    }

    function file_store_encrypted ($data, $access = 0, $blocked = 0) {
      file_check_tables ();

      for (;;) {
        $t = md5 (time ().'#SEPARATOR#'.time ());
        if (db_count ('files', "`name`=\"$t\"") == 0) {
          break;
        }
      }

      $d = config_get ('storage-enc');
      $t2 = $t;
      $t = $t.'.php';

      $fn = $d.'/'.$t;
      @ $f = fopen ($fn, 'wb');

      if (!$f) {
        return -1;
      }

      fwrite ($f, "<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  include '../../globals.php';
  include \$DOCUMENT_ROOT.'/inc/include.php';
  function _die(\$msg='') { header ('content-type: text/html;'); print ((\$msg!='')?(\$msg):('HACKERS?')); die; }
  session_start ();
  db_connect ();
  security_initialize ();
  \$fn=filename (\$PHP_SELF);
  \$d=db_row_value ('files', '`name`=\"'.\$fn.'\"');
  if (\$d['id']=='') _die ();
  if (\$d['access']>user_access ()) _die ('Access denied');
  if (\$d['blocked'] && !\$_SESSION['file_encrypted_allowed']) _die ('Content is blocked');
  header ('Content-Type: application/octet-stream');
  header ('Content-Disposition: attachment; filename=' . \$d['orig_name']);
  \$fp = fopen(__FILE__, 'r');
  fseek (\$fp, __COMPILER_HALT_OFFSET__);
  print (stream_get_contents (\$fp));
  __halt_compiler();");
  
      $f2 = fopen ($data['tmp_name'], 'rb');

      if ($f2) {
        $n = filesize ($data['tmp_name']);
        for ($i = 0; $i < $n; $i++) {
          $ch = fgetc ($f2);
          fwrite ($f, $ch);
        }
        fclose ($f2);
      }
      fclose ($f);

      db_insert ('files', array ('name' => db_string ($t),
                                 'orig_name' => db_string ($data['name']),
                                 'access' => $access,
                                 'blocked' => $blocked));

      return db_last_insert ();
    }
  
    function files_get_encrypted_link ($id) {
      $d = db_row_value ('files', "`id`=$id");

      if ($d['id']) {
        return config_get ('http-storage-enc').'/'.$d['name'];
      }

      return '';
    }

    function file_unlink_encrypted ($id) {
      if ($id == '') {
        return;
      }

      $d = db_row_value ('files', "`id`=$id");

      if ($d['id'] != '') {
        db_delete ('files', "`id`=$id");
        @ unlink (config_get ('storage-enc').'/'.$d['name']);
      }
    }

    function file_block_encrypted ($id, $blocked) {
      if ($blocked)
        db_update ('files', array ('blocked'=>1), "`id`=$id"); else
        db_update ('files', array ('blocked'=>0), "`id`=$id");
    }

    function file_allow_encrypted ($id, $val=true) {
      global $file_encrypted_allowed;
      $file_encrypted_allowed[$id] = $val;
    }

    function file_encrypted_on_user_logout () {
      global $file_encrypted_allowed;
      $file_encrypted_allowed = array ();
    }

    hook_register ('CORE.Security.OnUserLogout', file_encrypted_on_user_logout);
  }
?>