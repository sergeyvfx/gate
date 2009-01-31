<?php if ($PHP_SELF!='') {print 'HACKERS?'; die;} 
  // Creating the developers' specified navigate menu
  $datacontrol_menu=new CVCMenu ();
  $datacontrol_menu->Init ('dataManagmentMenu', 'type=hor;colorized=true;sublevel=1;border=thin;');
  $datacontrol_menu->AppendItem ('Разделы', config_get ('document-root').'/admin/content/', 'content');
  $datacontrol_menu->AppendItem ('Сервисы', config_get ('document-root').'/admin/service/', 'service');
?>