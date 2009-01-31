<?php
  function stencil_progress ($settings='') {
    $s=unserialize_params ($settings);
    if ($s['width']=='') $s['width']=100;
    $src='<div class="progress" style="width: '.$s['width'].'px; height: 6px;">';
    if ($s['pos']!='' && $s['pos']!='0') {
      $src.='<div class="progressStart"></div>';
      $src.='<div class="progressBar" style="width: '.floor ($s['pos']/100*$s['width']-2).'px;"></div>';
      $src.='<div class="progressEnd"></div>';
    } else $src.='<img src="pics/site/clear.gif">';
    $src.='</div>';
    return $src;
  }

  function progress ($settings='') { print stencil_prorgess ($settings); }
?>
