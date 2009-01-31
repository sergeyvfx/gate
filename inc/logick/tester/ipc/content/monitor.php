<?php if ($_WT_ipc_get_get_problem_desc_included_!='###WT_IPC_GetProblemDesc_Inclided###') { $_WT_ipc_get_problem_desc_included_='###WT_IPC_GetProblemDesc_Inclided###';

  function WT_IPC_Monitor () {

    $gw=WT_spawn_new_gateway ();
    $r=$gw->current_lib->IPC_Monitor ();

    if ($r!='') print setvars ($r); else print '&nbsp;';
  }

  ipc_register_function ('monitor', WT_IPC_Monitor);
}
?>
