<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for displaying list of datatypes
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

  formo ('title=Список существующих типов данных;');
  $i = 1;
  $n = db_affected ();
?>
  <table class="list smb">
    <tr class="h"><th class="n first">№</th><th width="40%">Название</th><th>Базовый класс</th><th width="48" class="last">&nbsp;</th></tr>
<?php
    while ($r = db_row ($q)) {
      $class = manage_datatype_get_by_name ($r['class']);
      $d = $r['refcount'] == 0;
?>
    <tr<?=(($i<$n)?(''):(' class="last"'))?>><td class="n"><?=$i;?>.</td><td><a href=".?action=edit&id=<?=$r['id'];?>"><?=$r['name'];?></a>&nbsp;(<?=$r['refcount'];?>)</td><td><?=$class['class'].(($class['pseudonym']!='')?(' - '.$class['pseudonym']):(''));?></td><td align="right"><?ibtnav ('edit.gif', '?action=edit&id='.$r['id'], 'Редакстировать элемент');?><?ibtnav (($d)?('cross.gif'):('cross_d.gif'), ($d)?('?action=delete&id='.$r['id']):(''), 'Удалить элемент', 'Удалить этот элемент?');?></td></tr>
<?php
      $i++;
    }
?>
  </table>
<?php
  formc ();
?>
