<?php if ($_WT_ipc_restore_task_included_!='###WT_IPC_RestoreTask_Inclided###') { $_WT_ipc_restore_task_included_='###WT_IPC_RestoreTask_Inclided###';

  function WT_RestoreTask () {
    global $id, $lid;
  
    if (!WT_IPC_CheckLogin ()) return;
    
    if (!isset ($id) || !isset ($lid)) { print ('Void filename for WT_RestoreTask'); return; }

    db_update ('tester_solutions', array ('status'=>0, 'errors'=>'""', 'points'=>0), "`id`=$id AND `lid`=$lid");
  }

  ipc_register_function ('restore_task', WT_RestoreTask);
}
?>
