<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Userlist generation script
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF!='') {
    print ('HACKERS?');
    die;
  }

  formo ('title=Список существующих пользователей;');
  $groups = group_list ();
?>
  <script language="JavaScript" type="text/javascript">
    function update () {
      var group=getElementById ('showGroup').value;
      nav ('.?group='+group);
    }
  </script>
<?php
  if (count ($list)>0) {
    global $page, $group;

    $perPage = opt_get ('user_count');
    if ($perPage<=0) {
      $perpage=10;
    }

    $pages = new CVCPagintation ();
    $pages->Init ('', (($group!='')?('urlprefix=?group\='.$group.';'):('')).'bottomPages=false;skiponcepage=true;');
    $i = 0;
    $n = count ($list);

    if ($page!='') {
      $pageid='&page='.$page;
    }

    while ($i < $n) {
      $c = 0;
      $pageSrc = '<table class="list">'."\n";
      $pageSrc .= '<tr class="h"><th class="n first">№</th><th width="20%">Логин</th><th width="20%">Имя</th><th width="20%">E-Mail</th><th>Уровень доступа</th><th width="48" class="last">&nbsp;</th></tr>'."\n";

      while ($c < $perPage && $i < $n) {
        $it = $list[$i];
        $d = !user_is_system ($it['id']);
        $pageSrc .= '<tr'.(($i==$n-1 || $c==$perPage-1)?(' class="last"'):('')).'><td class="n">'.($i+1).'.</td>'.
          '<td><a href=".?action=edit&id='.$it['id'].'&'.get_filters ().$pageid.'">'.$it['login'].'</a></td>'.
          '<td>'.$it['name'].'</td><td><a href="mailto:'.$it['email'].'" title="Отправить письмо">'.$it['email'].'</a></td>'.
          '<td>'.security_access_title ($it['access']).'</td>'.
          '<td align="right">'.stencil_ibtnav ('edit.gif', '?action=edit&id='.$it['id'].'&'.get_filters ().$pageid, 'Изменить элемент').
          stencil_ibtnav (($d)?('cross.gif'):('cross_d.gif'), ($d)?('?action=delete&id='.$it['id'].'&'.get_filters ().$pageid):(''), 'Удалить этот элемент', 'Удалить этот элемент?').
          '</td></tr>'."\n";
        $c++;
        $i++;
      }
      $pageSrc .= '</table>'."\n";
      $pages->AppendPage ($pageSrc);
    }
    $pages->Draw ();
  } else {
    info ('В этой группе нет пользователей');
  }
?>
  <div class="f">
    <form action="." method="POST" onsubmit="update (); return false;" onkeypress="if (event.keyCode==13) update ();">
      <b>Критерии выборки:</b>
      <table width="100%"><tr>
        <td width="120">Пользователи группы:</td><td>
        <select style="width: 300px;" id="showGroup">
          <option value="-1">Все</option>
<?php
  for ($i = 0; $i < count ($groups); $i++) {
    $g = $groups[$i];
?>
          <option value="<?=$g['id'];?>"<?=(($group==$g['id'])?(' selected'):(''))?>><?=$g['name'];?></option>
<?php
  }
?>
        </select></td>
        <td width="140" align="center"><button type="button" onclick="update ();" style="width: 120px;">Обновить</button>
        </td>
      </tr></table>
    </form>
  </div>
<?php
  formc ();
?>
