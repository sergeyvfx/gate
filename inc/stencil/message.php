<?php
  function stencil_info ($txt) {
    if ($txt!='')
      return '<div class="info"><table width="100%"><tr><td rowspan="2" class="img"><img src="'.config_get ('document-root').'/pics/info24.gif"></td>'.
        '<td class="title">Информация</td></tr><tr><td class="msg">'.$txt.'</td></tr></table></div>'; else
      return '';
  }
  function info ($txt) { print stencil_info ($txt); }
?>
