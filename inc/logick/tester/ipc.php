<?php if ($_WT_ipc_included_!='###WT_IPC_Inclided###') { $_WT_ipc_included_='###WT_IPC_Inclided###';

  $dirs=array (
    '/inc/logick/tester/ipc'
  );

  linkage ($dirs);

  function WT_IPC_CheckLogin () {
    global $login, $pass1, $pass2;
    return $login==config_get ('WT-IPC-Login') &&
           $pass1==config_get ('WT-IPC-Pass-1') &&
           $pass2==config_get ('WT-IPC-Pass-2');
  }
}
?>
