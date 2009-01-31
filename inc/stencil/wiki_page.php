<?php
  function stencil_wiki_page ($content, $tabs) {return tpl ('back/stencil/wiki_page', array ('content'=>$content, 'tabs'=>$tabs));}
  function wiki_page ($content, $tabs) { print stencil_wiki_page ($content, $tabs); }
?>
