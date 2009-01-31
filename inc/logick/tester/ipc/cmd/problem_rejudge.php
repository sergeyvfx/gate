<?php if ($_WT_ipc_cmd_problem_rejudge_included_!='###WT_IPC_ProblemRejudge_Inclided###') { $_WT_ipc_cmd_problem_rejudge_included_='###WT_IPC_ProblemRejudge_Inclided###';

  function WT_ProblemRejudge () {
    global $id;

    if ($id=='') return;

    $gw=WT_spawn_new_gateway ();
    if ($gw->current_lib->IPC_Problem_Rejudge ($id))
      print '+OK'; else print '-ERR';
  }

  ipc_register_function ('cmd_problem_rejudge', WT_ProblemRejudge);
}
?>
