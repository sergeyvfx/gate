<?php if ($PHP_SELF!='') {print 'HACKERS?'; die;} 
global $self;
$content=content_lookup (dirname ($self));
$subnav='';
if ($content!=null) { if ($content->GetAllowed ('READ')) {
  $pIFACE=$content->GetData ();
  $content->Editor_DrawContent (array ('subnav'=>$subnav));
} else {
    print ('${information}');
    add_info ('Извините, но просмотр содержимого этого разделя для Вас запрещен.');
  }
} else {
  print ('${information}');
  add_info ('Невозмонжно отобразить страницу, так как запрашиваемый раздел поврежден или не существует. Извините.');
}
?>
