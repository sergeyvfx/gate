<?php  if ($_ipc_executor_included_!='#ipc_executor_Included#') {$_ipc_executor_included_='#ipc_executor_Included#';

global $ipc;

/* Execute IPC command withot including all stuff  */

// Include required stuff
include $DOCUMENT_ROOT.'/inc/stuff/parsers.php';
include $DOCUMENT_ROOT.'/inc/stuff/linkage.php';
include $DOCUMENT_ROOT.'/inc/config.php';
include $DOCUMENT_ROOT.'/inc/common/config.php';
include $DOCUMENT_ROOT.'/inc/stuff/dbase.php';
include $DOCUMENT_ROOT.'/inc/stuff/ipc.php';

db_connect (false);

}
?>
