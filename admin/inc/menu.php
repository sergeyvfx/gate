<?php if ($PHP_SELF!='') {print 'HACKERS?'; die;} 
  global $DOCUMENT_ROOT;
  $manage_menu=new CVCMenu ();
  $manage_menu->Init ('ManageTopMenu', 'type=hor;colorized=true;hassubmenu=true;border=thin;');
  $manage_menu->AppendItem ('Управление данными',    config_get ('document-root').'/admin/content',        'control');
  $manage_menu->AppendItem ('Пользователи и группы', config_get ('document-root').'/admin/usergroup/user', 'usergroup');
  $manage_menu->AppendItem ('Настройки',             config_get ('document-root').'/admin/settings',       'settings');
  $manage_menu->AppendItem ('Разработчику',          config_get ('document-root').'/admin/dev/datatype',   'to-developer');
?>
