<?php if ($_WT_ipc_cmd_toggle_prusage_included_!='###WT_IPC_ToggleProblemUsage_Inclided###') { $_WT_ipc_cmd_toggle_prusage_included_='###WT_IPC_ToggleProblemUsage_Inclided###';

  function WT_ToggleProblemUsage () {
    global $cid, $id;

    if ($id=='' || $cid=='') return;

    $gw=WT_spawn_new_gateway ();
    if ($gw->current_lib->IPC_Contest_ToggleDisableProblem ($cid, $id))
      print '+OK'; else print '-ERR';
  }

  ipc_register_function ('cmd_problem_toggle_usage', WT_ToggleProblemUsage);
}
?>
