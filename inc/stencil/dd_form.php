<?php
  function stencil_dd_formo ($settings='') {
    global $dd_form_stuff_included, $CORE;
    $CORE->AddScriptFile ('dd_form.js');
    $s=unserialize_params ($settings);
    $onexpand=$s['onexpand']; if ($onexpand!='') $onexpand=' '.$onexpand;
    $onhide=$s['onhide'];     if ($onhide!='')   $onhide=' '.$onhide;
    return ('<div class="dd_form"><div id="title" class="dd_title"><table><tr><td>'.
      '<img src="'.config_get ('document-root').'/pics/arrdown_green.gif" id="show" onclick="dd_form_expand (this);'.$onexpand.'" alt="Развернуть" title="Развернуть">'.
      '<img src="'.config_get ('document-root').'/pics/arrup_red.gif" style="display: none;" id="hide" onclick="dd_form_hide (this);'.$onhide.'" alt="Свернуть" title="Свернуть">'.
      '</td><td>'.$s['title'].'</td></tr></table></div><div class="dd_content" id="content">');
  }
  function stencil_dd_formc () {
    return ('</div></div>');
  }

  function dd_formo ($settings='') { print stencil_dd_formo ($settings); }
  function dd_formc () { print stencil_dd_formc (); }
?>
