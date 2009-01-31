<?php
  if ($PHP_SELF!='') {print ('HACKERS?'); die;}
  global $id;
  $c=wiki_spawn_content ($id);
  print ('<div id="snavigator"><a href=".">Разделы</a>'.wiki_content_navigator ($id, 'action=editor')).'</div>';
  editor_draw_menu ();
  $c->Editor_ManageEditForm ();
?>
