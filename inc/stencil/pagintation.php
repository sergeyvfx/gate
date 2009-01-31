<?php if ($_SETCIL_Pagintation_!='123STENCIL_Pagintation321') { $_SETCIL_Pagintation_='123STENCIL_Pagintation321';

  function stencil_pagintation ($count, $current=0, $url_prefix='', $pageid='pageid') {
    if ($url_prefix=='')
      $url_prefix=content_url_get_full ();
    $res='<div class="pagintation">Страницы: ';

    if (isset ($GLOBALS[$pageid]))
      $current=$GLOBALS[$pageid];

    if ($current<0) $current=0;
    if ($current>=$count) $current=$count-1;

    for ($i=0; $i<$count; $i++) {
      if ($i!=$current)
        $t='<a href="'.$url_prefix.(($url_prefix[strlen ($url_prefix)-1]=='?')?(''):('&')).$pageid.'='.$i.'">'.($i+1).'</a>'; else
        $t='['.($i+1).']';
      $res.=$t;
    }

    $res.='</div>';
    return $res;
  }

  function pagintation ($count, $current=0, $url_prefix='', $pageid='pageid') { print stencil_pagintation ($count, $current, $url_orefix, $pageid); }
}
?>
