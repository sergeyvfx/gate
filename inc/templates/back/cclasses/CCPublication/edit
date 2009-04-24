<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Edit script for publication
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF != '') {
    print ('HACKERS?');
    die;
  }

  global $self, $wiki, $action, $id;
  $content = content_lookup (dirname ($self));

  if ($content != null) {
    if (!$content->GetAllowed ('EDIT')) {
      header ('Location: .');
    } else {
?>
<div id="navigator">Редактирование статьи</div>
<div class="contentSub">Статья: &laquo;<a href="."><?=$content->GetName ();?></a>&raquo;</div>
${information}
<?php
      if (user_id () < 0) {
        add_info ('Вы не представились системе. Выйдите из этого раздела, если вы не нуждаетесь во внесении изменений в эту статью. '.
          'Так или иначае, Ваш IP-адрес протрассирован и сохранен, все Ваши действия журналируются. Если Вы совершите противозаконные действия, оговорённые УК РФ, то мы оставляем за собой право сообщить об этих действиях в правоохранительные органы.');
      }

      $content->Editor_EditForm ();
    }
  } else {
    print ('${information}');
    add_info ('Невозмонжно отобразить страницу, так как запрашиваемый раздел поврежден или не существует. Извините.');
  }
?>
