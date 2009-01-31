<?php if ($_config_included_!='#config_Included#') {$_builtin_included_='#config_Included#';
  function config_set ($key,$val) {global $config; $config[$key]=$val;}
  function config_get ($key) {global $config; return $config[$key];}
}
?>
