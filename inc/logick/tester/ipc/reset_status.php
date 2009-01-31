<?php if ($_WT_ipc_reset_status_included_!='###WT_IPC_ResetStatus_Inclided###') { $_WT_ipc_reset_status_included_='###WT_IPC_ResetStatus_Inclided###';

  function WT_ResetStatus () {
    if (!WT_IPC_CheckLogin ()) return;
    db_update ('tester_solutions', array ('status'=>0), '`status`=1');
  }

  ipc_register_function ('reset_status', WT_ResetStatus);
}
?>
