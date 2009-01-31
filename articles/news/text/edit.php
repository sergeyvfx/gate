<?php if ($PHP_SELF!='') {print 'HACKERS?'; die;} 
global $self, $wiki, $action, $id;
$content=content_lookup (dirname (dirname ($self)));
$pIFACE=$content->GetData ();
if ($content!=null) {
?>
<?php
  if (!$content->GetAllowed ('EDIT')) { header ('Location: .'); } else {
?>
<div id="navigator">Редактирование статьи</div>
<div class="contentSub">Статья: &laquo;<a href="."><?=$content->GetName ();?></a>&raquo;</div>
${information}
<?php
    if (user_id ()<0) {
      add_info ('Вы не представились системе. Выйдите из этого раздела, если вы не нуждаетесь во внесении изменений в эту статью. '.
        'Так или иначае, Ваш IP-адрес протрассирован и сохранен, все Ваши действия журналируются. Если Вы совершите противозаконные действия, оговорённые УК РФ, то мы оставляем за собой право сообщить об этих действиях в правоохранительные органы.');
    }
    $pIFACE->Editor_ItemEditForm ();
  }
} else {
  print '${information}';
  add_info ('Невозмонжно отобразить страницу, так как запрашиваемый раздел поврежден или не существует. Извините.');
}
?>
