<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Get problem information for uploading
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

  if ($_WT_ipc_get_problem_included_ != '###WT_IPC_GetProblem_Inclided###') {
    $_WT_ipc_get_problem_included_ = '###WT_IPC_GetProblem_Inclided###';

    function WT_GetProblem () {
      global $lid;

      if (!WT_IPC_CheckLogin ()) {
        return;
      }

      if ($lid == '') {
        return;
      }

      $q = db_select ('tester_problems', array ('*'),
                      '(`uploaded`=FALSE) AND (`lid`='.$lid.')',
                      'ORDER BY `id` LIMIT 1');
      if (db_affected () <= 0) {
        return;
      }

      $r = db_row ($q);

      $s = unserialize ($r['settings']);

      $arr = array ();

      $arr['ID'] = $r['id'];
    
      if (isset ($s['filename'])) {
        $arr['FILENAME'] = $s['filename'];
      }

      if (preg_match ('/[0-9]+/', ($s['checker']))) {
        $arr['CHECKER'] = $s['checker'];
      }

      print db_pack ($arr);
    }

    ipc_register_function ('get_problem', WT_GetProblem);
  }
?>
