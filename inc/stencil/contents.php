<?php if ($stencil_contents_included!='#stencil_contents_Included#') {$stencil_contents_included='#stencil_contents_Included#';

global $contents_builtin_Included;
$contents_builtin_Included=false;

$contents_count=0;

function contents_builtin () {
  global $contents_builtin_Included, $CORE;
  if ($contents_builtin_Included) return;
  $CORE->AddStyle ('contents');
  tplp ('back/stencil/contents_script');
  $contents_builtin_Included=true;
}

function stencil_contentso ($settings='') {
  global $contents_count;
  contents_builtin ();
  $s=unserialize_params ($settings);
  if ($s['id']=='') {
    $id='contents_'.$contents_count;
    $contents_count++;
  } else $id=$s['id'];
  $title=htmlspecialchars ((trim ($s['title'])!='')?($s['title']):('Содержание'));
  $visible=true;
  if ($_COOKIE['contents_'.$id.'_folded']=='false') $visible=false;
  return
'<div class="contents" id="'.$id.'">
  <div id="title"><span id="text">'.$title.'</span><span id="action">[<a id="hide" href="#" onclick="HideContents (\''.$id.'\'); return false;"'.(($visible)?(''):(' class="invisible"')).'>Убрать</a><a id="show" href="#" onclick="ShowContents (\''.$id.'\'); return false;"'.(($visible)?(' class="invisible"'):('')).'>Показать</a>]</span></div>
  <div id="body"'.(($visible)?(''):(' class="invisible"')).'>
';
}

function stencil_contentsc ($settings='') { $s=unserialize_params ($settings); return '</div></div>'."\n"; }

function contentso ($settings='') { print stencil_contentso ($settings); }
function contentsc ($settings='') { print stencil_contentsc ($settings); }

}
?>
