<?php if ($_SOCK_Included_!='##SOCK_Included##') { $_SOCK_Included_='##SOCK_Included##';

  function sock_connect ($server, $port, $timeout=0) {
    if (!$timeout)
      @ $sock=fsockopen ($server, $port); else
      @ $sock=fsockopen ($server,$port,&$errno,&$errstr,$timeout);
    return $sock;
  }

  function sock_write ($sock, $buf, $len)    { fwrite ($sock, $buf, $len); }
  function sock_read  ($sock, $len, $all=false) { return fread ($sock, $len); }

  function sock_read_all  ($sock, $len) {
    $s='';
    $first=true;
    while (true) {
      if ($first) usleep (50000);
      usleep (50000);
      $t=fread ($sock, $len);
      if ($t=='') break;
      $s.=$t;
      $first=false;
    }
    return $s; 
  }

  function sock_set_blocking ($socket, $val) { set_socket_blocking ($socket, $val); }
}
?>
