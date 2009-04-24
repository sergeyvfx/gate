<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for sections list generation
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

  formo ('title=Список существующих разделов;');
  $arr = content_Registered_CClasses ();
  $n = count ($arr);

  global $cclasses;

  for ($i = 0; $i < $n; $i++) {
    $cclasses[$arr[$i]['class']] = $arr[$i]['pseudonym'];
  }
?>

<script language="JavaScript" type="text/javascript">
  function update_element_parent (src, dst) {
    if (src!=1 && src!=dst)
      cnav ('Вы действительно хотите перетащить этот элемент?', '.?action=setparent&id='+src+'&pid='+dst);
  }
</script>

<?php
  function get_contents_by_pid ($pid, $list) {
    $arr = array ();
    for ($i = 0; $i < count ($list); $i++) {
      if ($list[$i]['pid'] == $pid) {
        $arr[] = $list[$i];
      }
    }
    return $arr;
  }

  function print_contents ($pid, $list, $pIndexes = array (), $path = '', $afterInfo = false) {
    global $cclasses;
    $arr = get_contents_by_pid ($pid, $list);
    $n = count ($arr);
    $cntPrefix = '';

    for ($i = 0; $i < count ($pIndexes); $i++) {
      $cntPrefix .= $pIndexes[$i].'.';
    }

    $depth = count ($pIndexes);
    for ($i = 0; $i < $n; $i++) {
      $r = $arr[$i];
      $cntString = $cntPrefix.($i+1);
      $p = $path.'/'.$r['path'];
      $childs = get_contents_by_pid ($r['id'], $list);
      $nChilds = count ($childs);
?>
  <div style="padding-left: <?=($depth * 24)?>px;"><table class="list<?=(($nChilds==0 && $i==$n-1 && !$afterInfo)?(' smb'):(''));?>">
    <tr <?=(($nChilds==0 && $i==$n-1 && !$afterInfo)?('class="last"'):(''));?>><td class="n"><?=$cntString;?>.</td><td width="86"><?dnd_anchor ($r['id'], update_element_parent, '');?><?ibtnav (($i!=$n-1)?('arrdown_blue.gif'):('arrdown_d.gif'), ($i!=$n-1)?('?action=down&id='.$r['id']):(''), 'Опустить');?><?ibtnav (($i!=0)?('arrup_blue.gif'):('arrup_d.gif'), ($i!=0)?('?action=up&id='.$r['id']):(''), 'Поднять');?></td><td><a href=".?action=editor&id=<?=$r['id'];?>"><?=$r['name'];?></a>&nbsp;&nbsp;(<?=$p;?>)</td><td width="250"><?=$cclasses[$r['class']];?></td><td width="48" align="right"><?ibtnav ('prefs.gif', '?action=edit&id='.$r['id'], 'Изменить элемент');?><?ibtnav ('cross.gif', '?action=delete&id='.$r['id'], 'Удалить элемент', 'Удалить этот элемент?');?></td></tr>
  </table></div>

<?php
      $printed = true;
      $s_pIndexes = $pIndexes;
      $pIndexes[] = $i + 1;
      print $counter;
      print_contents ($r['id'], $list, $pIndexes, $p, $i!=$n-1);
      $pIndexes = $s_pIndexes;
    }
  }
?>

  <table class="list">
    <tr class="hs"><th width="24" class="first"><?dnd_anchor (1, update_element_parent, '', false);?></th><th>Корень сайта</th><th width="24" align="right" class="last"><?ibtnav ('prefs.gif', '?action=edit&id=1', 'Настроить права доступа');?></th></tr>
  </table>

<?php
  print_contents (1, $list);
  formc ();
?>
