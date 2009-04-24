<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Get task list
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

  if ($_WT_ipc_get_task_list_included_ != '##WT_IPC_GetTaskList_Inclided##') {
    $_WT_ipc_get_task_list_included_ = '##WT_IPC_GetTaskList_Inclided##';

    function WT_GetTaskList () {
      if (!WT_IPC_CheckLogin ()) {
        return;
      }

      $q = db_query ('SELECT `ts`.`id`, `ts`.`lid` '.
                     'FROM `tester_solutions` AS `ts`, '.
                     '`tester_problems` AS `tp` '.
                     'WHERE (`ts`.`status`=0) AND '.
                     '(`ts`.`problem_id`=`tp`.`id`) AND '.
                     '(`tp`.`uploaded`=2) ORDER BY `timestamp` LIMIT 15');

      while ($r=db_row ($q)) {
        println ($r['id'].'@'.$r['lid']);
      }
    }

    ipc_register_function ('get_task_list', WT_GetTaskList);
  }
?>
