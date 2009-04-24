<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Delete task from testing queue
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

  if ($_WT_ipc_delete_task_included_ != '###WT_IPC_DeleteTask_Inclided###') {
    $_WT_ipc_delete_task_included_ = '###WT_IPC_DeleteTask_Inclided###';

    function WT_DeleteTask () {
      global $id, $lid;

      if (!WT_IPC_CheckLogin ()) {
        return;
      }

      if (!isset ($id) || !isset ($lid)) {
        print ('Void filename for WT_DeleteTask');
        return;
      }

      db_update ('tester_solutions', array ('status' => 1),
                 "`id`=$id AND `lid`=$lid");
    }

    ipc_register_function ('delete_task', WT_DeleteTask);
  }
?>
