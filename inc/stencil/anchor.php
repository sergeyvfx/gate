<?php
  $anchor_appened=false;
  function stencil_dnd_anchor ($id=-1, $callback='', $hint='', $dragable=true) {
    global $anchor_appened, $CORE;
    $CORE->AddScriptFile ('anchor.js');
    add_body_handler ('onload', 'anchor_Register', array ("'$id'", $callback));
    if (!$anchor_appened) {
      add_body_handler ('onmousemove', 'anchor_OnMouseMove',  array ('event'));
      add_body_handler ('onscroll',    'anchor_OnPageScroll', array ('event'));
      add_body_handler ('onmouseup',   'anchor_StopDrag',     array ());
      $anchor_appened=true;
    }
    return tpl ('back/stencil/anchor', array ('id'=>$id, 'callback'=>$callback, 'hint'=>$hint, 'dragable'=>$dragable));
  }
  function dnd_anchor ($id=-1, $callback='', $hint='', $dragable=true) { print stencil_dnd_anchor ($id, $callback, $hint, $dragable);}
?>
