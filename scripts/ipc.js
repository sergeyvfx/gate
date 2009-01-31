function IPC_Send_Command_Callback (http_request, callback) {
  if (http_request.readyState==4) {
    CONTENT_FreeOpening ();
    if (callback)
      callback (http_request.responseText);
  }
}

function IPC_Send_Command (addr, post, callback) {
  ipc_send_request (addr, post,  function (http_request) { IPC_Send_Command_Callback (http_request, callback); });
  CONTENT_SetOpening ();
}
