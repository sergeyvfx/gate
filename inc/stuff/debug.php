<?php if ($_debug_included_!='#debug_Included#') {$_debug_included_='#debug_Included#'; 
  function debug_watchdog_clear () {global $debug_watchdog; $debug_watchdog=mtime ();}
  function debug_get_watchdog () {global $debug_watchdog; return mtime ()-$debug_watchdog;}
}
?>