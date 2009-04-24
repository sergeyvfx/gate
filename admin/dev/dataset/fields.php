<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for fields list displaying
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

  global $id, $eid;

  formo ('title=Поля набора данных;');
  $d = manage_dataset_refcount ($id);
  redirector_add_skipvar ('act', 'togimp');
?>
  <table class="list smb">
    <tr class="h">
      <th class="n first">№</th><th width="40%">Название</th><th>Поле</th><th>Тип</th><th width="118" class="last">&nbsp;</th>
    </tr>
<?php
    for ($i = 0; $i < count ($list); $i++) {
      $r = $list[$i];
      $s = unserialize ($r['settings']);
      $class = manage_spawn_datatype ($r['datatype']);
      $imp = $s['important'];
      $invis = $s['invisible'];
?>
    <tr<?=(($i < count ($list) - 1)?(''):(' class="last"'))?>>
      <td class="n"><?=($i + 1);?>.</td>
      <td><a href=".?action=edit&id=<?=$id;?>&viewelement=<?=$r['id']?>"><?=$r['title'];?></a></td><td><?=$r['field'];?></td><td><?=$class->GetName ();?></td>
      <td width="148" align="right">
        <?ibtnav (($i != count ($list) - 1) ? ('arrdown_blue.gif') : ('arrdown_d.gif'), ($i != count ($list) - 1) ? ('?action=edit&id='.$id.'&act=down&eid='.$r['id']) : (''), 'Опустить');?>
        <?ibtnav (($i != 0) ? ('arrup_blue.gif'):('arrup_d.gif'), ($i != 0)?('?action=edit&id='.$id.'&act=up&eid='.$r['id']):(''), 'Поднять');?>
        <?ibtnav (($imp) ? ('info_d.gif') : ('info.gif'), '?action=edit&id='.$id.'&act=togimp&eid='.$r['id'], ($imp) ? ('Обязательное') : ('Необязательное'));?>
        <?ibtnav (($invis) ? ('show_d.gif') : ('show.gif'), '?action=edit&id='.$id.'&act=togvis&eid='.$r['id'], ($invis) ? ('Невидимое') : ('Видимое'));?>
        <?ibtnav ('edit.gif', '?action=edit&id='.$id.'&viewelement='.$r['id'], 'Редактировать элемент');?>
<?php
  if ($d == 0) {
    ibtnav ('cross.gif', '?action=edit&id='.$id.'&act=delete&eid='.$r['id'], 'Удалить элемент', 'Удалить этот элемент?');
  } else {
    ibtnav ('cross_d.gif', '', 'Удалить элемент', 'Удалить этот элемент?');
  }
?>
      </td>
    </tr>
<?php
    }
?>
  </table>

<?php
  formc ();
  redirector_add_skipvar ('act', 'down');
  redirector_add_skipvar ('act', 'up');
?>
