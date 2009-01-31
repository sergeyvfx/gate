<?php
  function stencil_core_page ($content, $caption='Системная страница') {
    return tpl ('back/stencil/core_page', array ('content'=>$content, 'caption'=>$caption));
  }
  function core_page ($content, $caption='Системная страница') { print stencil_core_page ($content, $caption); }
?>
