<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Reset status of solutions
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

  if ($_WT_ipc_restore_task_included_ != '###WT_IPC_RestoreTask_Inclided###') {
    $_WT_ipc_restore_task_included_ = '###WT_IPC_RestoreTask_Inclided###';

    function WT_RestoreTask () {
      global $id, $lid;
  
      if (!WT_IPC_CheckLogin ()) {
        return;
      }
    
      if (!isset ($id) || !isset ($lid)) {
        print ('Void filename for WT_RestoreTask');
        return;
      }

      db_update ('tester_solutions', array ('status' => 0, 'errors' => '""',
                                            'points' => 0),
                 "`id`=$id AND `lid`=$lid");
    }

    ipc_register_function ('restore_task', WT_RestoreTask);
  }
?>
