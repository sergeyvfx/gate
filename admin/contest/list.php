<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for servicdes list generation
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

  formo ('title=Список существующих конкурсов;');
?>
  <table class="list smb">
    <tr class="h">
        <th width="5%" class="n first">№</th>
        <th width="85%">Название</th>
        <th width="10%" class="last">&nbsp;</th></tr>
<?php
  $n = count ($list);
  
  for ($i = 0; $i < $n; $i++) {
    $it = $list[$i];
    $d = true;
?>
    <tr<?=(($i == $n - 1)?(' class="last"'):(''));?>><td class="n"><?=$i + 1;?>.</td><td><a href=".?action=edit&id=<?=$it['id'];?>"><?=$it['name'];?></a></td><td align="right"><?ibtnav ('edit.gif', '?action=edit&id='.$it['id'], 'Изменить элемент');?><?ibtnav (($d)?('cross.gif'):('cross_d.gif'), ($d)?('?action=delete&id='.$it['id']):(''), 'Удалить элемент', 'Удалить этот элемент?');?></td></tr>
<?php
  }
?>
  </table>
<?php
  formc ();
?>
