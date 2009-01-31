<?php if ($_Log_included_!='#log_Included#') { $_Log_included_=='#log_Included#';
  function _log ($module, $status, $message) {
    $carr=service_by_classname ('CSCLog');
    if (!isset ($carr[0])) return false;
    $carr[0]->service->AppendEntry ($module, $status, $message);
  }
  
  function _log_message ($module, $message) { _log ($module, 'MESSAGE', $message); }
  function _log_error   ($module, $message) { _log ($module, 'ERROR',   $message); }
  function _log_warning ($module, $message) { _log ($module, 'WARNING', $message); }
  
  function core_log    ($status, $message) { _log ('CORE', $status, $message); }
  function core_log_message ($message) { core_log ('MESSAGE', $message); }
  function core_log_error   ($message) { core_log ('ERROR',   $message); }
  function core_log_warning ($message) { core_log ('WARNING', $message); }
}
?>
