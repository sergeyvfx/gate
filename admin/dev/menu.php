<?php if ($PHP_SELF!='') {print ('HACKERS?'); die;}
  // Creating the developers' specified navigate menu
  $mandev_menu=new CVCMenu ();
  $mandev_menu->Init ('andevMenu', 'type=hor;colorized=true;sublevel=1;border=thin;');
  $mandev_menu->AppendItem ('Типы данных',      config_get ('document-root').'/admin/dev/datatype/',  'datatype');
  $mandev_menu->AppendItem ('Наборы данных',    config_get ('document-root').'/admin/dev/dataset/',   'datasets');
  $mandev_menu->AppendItem ('Хранилища данных', config_get ('document-root').'/admin/dev/storages/',  'storages');
  $mandev_menu->AppendItem ('Шаблоны',          config_get ('document-root').'/admin/dev/templates/', 'templates');
  $mandev_menu->AppendItem ('Браузер XPFS',     config_get ('document-root').'/admin/dev/xpfs/',      'xpfs');
?>