<?php if ($_WT_ipc_get_get_problem_desc_included_!='###WT_IPC_GetProblemDesc_Inclided###') { $_WT_ipc_get_problem_desc_included_='###WT_IPC_GetProblemDesc_Inclided###';

  function WT_GetProblemDesc () {
    global $id, $cid, $backlink;

    if ($id=='' || $cid=='') return;

    $gw=WT_spawn_new_gateway ();
    $r=$gw->current_lib->IPC_Problem_DescriptionForm ($id, $cid, $backlink);

    if ($r!='') print setvars ($r); else print '&nbsp;';
  }

  ipc_register_function ('get_problem_desc', WT_GetProblemDesc);
}
?>
