<?php
  function stencil_on_construction () {
    return tpl ('back/on_construction');
  }
  function on_construction () { print stencil_on_construction (); }
?>
