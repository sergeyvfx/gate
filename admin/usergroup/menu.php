<?php if ($PHP_SELF!='') {print ('HACKERS?'); die;}
  // Creating the developers' specified navigate menu
  $usergroup_menu=new CVCMenu ();
  $usergroup_menu->Init ('andevMenu', 'type=hor;colorized=true;sublevel=1;border=thin;');
  $usergroup_menu->AppendItem ('Пользователи', config_get ('document-root').'/admin/usergroup/user/', 'user');
  $usergroup_menu->AppendItem ('Группы', config_get ('document-root').'/admin/usergroup/group/', 'group');
?>