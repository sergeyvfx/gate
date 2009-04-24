<?php 
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Hooks' stuff
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

  if ($_hook_included_ != '#hook_Included#') {
    $_hook_included_ = '#hook_Included#';
    $hooks = array ();
  
    function hook_register ($callback, $handler) {
      global $hooks;
      $hooks[$callback][] = $handler;
    }

    function hook_call ($callback, $user_data=0) {
      global $hooks;
      for ($i = 0, $n = count ($hooks[$callback]); $i < $n; $i++) {
        $hooks[$callback][$i] ($user_data);
      }
    }

    function hook_linkage () {
      global $DOCUMENT_ROOT;
      $path = $DOCUMENT_ROOT.'/inc/logick';
      $dir = opendir ($path);

      while (($file = readdir ($dir)) != false) {
        if ($file!='.' && $file!='..') {
          $full = $path.'/'.$file;
          if (is_dir ($full) && file_exists ($full.'/hook.php')) {
            include $full.'/hook.php';
          }
        }
      }
    }

    hook_linkage ();
  }
?>
