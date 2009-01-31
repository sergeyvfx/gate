<?php
  function stencil_tframe ($content, $caption='') {return tpl ('back/stencil/frame', array ('content'=>$content, 'caption'=>$caption));}
  function stencil_groupo ($p='')  { $p=unserialize_params ($p); return ('<div class="group"><span class="title">'.$p['title'].'</span><div class="content">'."\n"); }
  function stencil_groupc ()       { return ('</div></div>'."\n"); }
  
  function stencil_lblocko ($p=array ()) {
    $p=unserialize_params ($p);
    if ($p['color']!='') $color=' t'.$p['color'];
    if ($p['bgcolor']!='') $bg=' style="background-color: '.$p['bgcolor'].'"';
    return '<div class="lblock"><div class="title'.$color.'">'.htmlspecialchars ($p['title']).'</div><div class="content"'.$bg.'>';
  }

  function stencil_lblockc () { return '</div></div>'; }

  function tframe ($content, $caption='') { print stencil_tframe ($content, $caption); }
  function groupo ($p='') { print stencil_groupo ($p); }
  function groupc ()      { print stencil_groupc (); }

  function lblocko ($p=array ()) { print stencil_lblocko ($p); }
  function lblockc ()            { print stencil_lblockc (); }
?>
