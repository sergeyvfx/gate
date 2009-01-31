<?php
  function stencil_imaged_href ($title, $href, $img) {
    return tpl ('back/stencil/imaged_href', array ('title'=>$title, 'href'=>$href, 'img'=>$img));
  }
  function stencil_ibtnav ($img, $nav, $title='', $confirm='') {
    $scr=(($confirm!='')?("if (cfrm ('$confirm'))"):(''))."nav ('$nav')";
    return ('<img class="'.(($nav!='')?('btn'):('btnd')).'" src="'.config_get ('document-root').'/pics/'.$img.'"'.(($nav!='')?(' onclick="'.$scr.'"'):('')).' '.(($title!='')?('title="'.$title.'" alt="'.$title.'"'):('')).'>');
  }
  function stencil_titledimg ($img, $title) {
    return ('<img src="'.config_get ('document-root')."/pics/$img\"".(($title!='')?(" title=\"$title\""):('')).'>');
  }
  function stencil_imghelp ($title) { stencil_titledimg ('help.gif', $title); }
  function stencil_cbimage ($img, $onclick, $title='') {
    return '<img src="'.config_get ('document-root').'/pics/'.$img.'" onclick="'.$onclick.'"'.(($title!='')?(' title="'.$title.'"'):('')).' alt="'.$title.'" class="pointer">';
  }

  function imaged_href ($title, $href, $img) { print stencil_imaged_href ($title, $href, $img); }
  function ibtnav ($img, $nav, $title='', $confirm='') { print stencil_ibtnav ($img, $nav, $title, $confirm); }
  function titledimg ($img, $title) { print stencil_titledimg ($img, $title); }
  function imghelp ($title) { print stencil_imghelp ($title); }
  function cbimage ($img, $onclick, $title='') { print stencil_cbimage ($img, $onclick, $title); }
?>
