<?php
  if ($PHP_SELF!='') {print ('HACKERS?'); die;}
  global $id;
  $c=manage_spawn_service ($id);
  print '<div id="snavigator"><a href=".">Сервисы</a>'.$c->GetName ().'</div>';
  editor_draw_menu ();
  $c->Editor_ManageEditForm ();
?>
