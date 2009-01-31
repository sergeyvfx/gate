<?php if ($_WT_ipc_get_task_list_included_!='###WT_IPC_GetTaskList_Inclided###') { $_WT_ipc_get_task_list_included_='###WT_IPC_GetTaskList_Inclided###';

  function WT_GetTaskList () {
  
    if (!WT_IPC_CheckLogin ()) return;

//    $q=db_select ('tester_solutions', array ('id', 'lid'), '`status`=0', 'ORDER BY `timestamp`');
    $q=db_query ('SELECT `ts`.`id`, `ts`.`lid` FROM `tester_solutions` AS `ts`, `tester_problems` AS `tp` WHERE (`ts`.`status`=0) AND (`ts`.`problem_id`=`tp`.`id`) AND (`tp`.`uploaded`=2) ORDER BY `timestamp` LIMIT 15');
    while ($r=db_row ($q)) {
      print $r['id'].'@'.$r['lid']."\n";
    }
  }
  ipc_register_function ('get_task_list', WT_GetTaskList);
}
?>
