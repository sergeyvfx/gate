<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Grouplist generation script
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

  formo ('title=Список существующих групп;');
?>
  <table class="list smb">
    <tr class="h"><th width="24" class="n first">№</th><th width="40%">Имя</th><th>Группа `по умолчанию`</th><th width="48" class="last">&nbsp;</th></tr>
<?php
    for ($i = 0; $i < count ($list); $i++) {
      $r = $list[$i];
?>
    <tr<?=(($i<count ($list)-1)?(''):(' class="last"'))?>><td class="n"><?=($i+1);?>.</td><td><a href=".?action=edit&id=<?=$r['id'];?>"><?=$r['name'];?></a> (<?=group_users_inside ($r['id']);?>)</td><td><?=(($r['default'])?('Да'):('Нет'))?></td><td align="right"><?ibtnav ('edit.gif', '?action=edit&id='.$r['id'], 'Изменить элемент');?><?ibtnav ('cross.gif', '?action=delete&id='.$r['id'], 'Удалить элемент', 'Удалить этот элемент?');?></td></tr>
<?php
    }
?>
  </table>
<?php
  formc ();
?>
