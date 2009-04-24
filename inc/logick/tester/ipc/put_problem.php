<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Put problem uploading status
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

  if ($_WT_ipc_put_problem_included_ != '###WT_IPC_PutProblem_Inclided###') {
    $_WT_ipc_put_problem_included_ = '###WT_IPC_PutProblem_Inclided###';

    function WT_PutProblem () {
      global $id, $lid, $err, $desc;

      if (!WT_IPC_CheckLogin ()) {
        return;
      }

      if ($id  == '')  {
        print ('Void filename for WT_PutProblem()');
        return;
      }

      if ($lid == '') {
        print ('Void library identifier for WT_PutProblem()');
        return;
      }

      $data = db_row_value ('tester_problems', "(`id`=$id) AND (`lid`=$lid)");
      $s = unserialize ($data['settings']);

      $s['ERR']  = $err;
      $s['DESC'] = $desc;
      unset ($s['filename']);

      db_update ('tester_problems', array ('uploaded' => (($err!='OK')?(1):(2)),
                                           'settings' => db_string (serialize ($s))),
                 "(`id`=$id) AND (`lid`=$lid)");
    }

    ipc_register_function ('put_problem', WT_PutProblem);
  }
?>
