<?php if ($_hanlder_included_!='#handler_Included#') {$_hanlder_included_='#handler_Included#';
  $handlers=array ();
  function handler_add ($body, $handler, $callback, $params=array ()) {
    global $handlers;
    $handlers[$body][$handler][]=array ('callback'=>$callback, 'params'=>$params);
  }
  function add_body_handler ($handler, $callback, $params=array ()) { handler_add ('body', $handler, $callback, $params); }

  function handler_get_list ($body, $handler='') {
    global $handlers;
    if ($handler=='')
      return $handlers[$body];
    return $handlers[$body][$handler];
  }
  function get_body_handlers ()     { return handler_get_list ('body'); }
  
  function handler_build_callback ($callback) {
    $res=$callback['callback'].' (';
    $printend=false; $params=$callback['params'];
    for ($i=0; $i<count ($params); $i++) {
      if ($printed) $res.=', ';
      $res.=$params[$i];
      $printed=true;
    }
    $res.=')';
    return $res;
  }
}
?>
