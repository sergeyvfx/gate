<?php
  global $tabctrl_stuff_included;
  $tabctrl_stuff_included=false;
  function tabctrl_include_stuff () {
    global $tabctrl_stuff_included, $CORE;
    if ($tabctrl_stuff_included) return;
    $CORE->AddStyle ('tabctrl');
    $tabctrl_stuff_included=true;
  }

  function stencil_tabo ($settings)    { tabctrl_include_stuff (); $s=unserialize_params ($settings); return tpl ('back/stencil/tabo', $s); }
  function stencil_tabc ($settings='') { $s=unserialize_params ($settings); return tpl ('back/stencil/tabc', $s); }

  function tabo ($s)    { print (stencil_tabo ($s)); }
  function tabc ($s='') { print (stencil_tabc ($s)); }  
?>
