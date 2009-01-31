<?php if ($PHP_SELF!='') {print 'HACKERS?'; die;}
  $start_root=opt_get ('start_root');
  if ($start_root!='' && $start_root!='/') redirect (config_get ('document-root').$start_root);

  $tpl=manage_template_by_name ('Статьи / Заглавная страница');
  print ($tpl->GetText ());
?>
