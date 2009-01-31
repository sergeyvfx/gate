<?php if ($PHP_SELF!='') {print 'HACKERS?'; die;} 
global $self, $wiki, $action, $history, $id, $oldid;
$content=content_lookup (dirname ($self));
$subnav='';
if ($content!=null) { if ($content->GetAllowed ('READ')) {
  $pIFACE=$content->GetData ();
  if ($oldid!='' && $content->GetAllowed ('EDIT'))
    $subnav='<div class="contentSub">Содержимое статьи на: <b>'.format_ltime ($pIFACE->GetTimestamp ()).'</b></div>';
  $content->Editor_DrawContent (array ('subnav'=>$subnav));
  draw_template ('Статья / Нижний колонтитул');
} else {
    print ('${information}');
    add_info ('Извините, но просмотр содержимого этого разделя для Вас запрещен.');
  }
} else {
  print ('${information}');
  add_info ('Невозмонжно отобразить страницу, так как запрашиваемый раздел поврежден или не существует. Извините.');
}
?>