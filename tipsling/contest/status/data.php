<?php
/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */
if ($PHP_SELF != '') {
  print 'HACKERS?';
  die;
}

global $current_contest;

if ($current_contest =='' || $current_contest == -1)
    header('Location: ../choose');

$contest = contest_get_by_id($current_contest);
$task_count = 20;
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a>Монитор</div>
${information}
<? formo('title=Состояние присланных заданий;'); ?>
<!--<div class="f" style="margin: 6px -6px 6px;">
  <form action="." method="POST" onsubmit="update (); return false;" onkeypress="if (event.keyCode==13) update ();">
    <table width="100%">
      <tr>
        <td>
          <b>Конкурс: &nbsp;</b>
          <select id="ContestGroup" onchange="update()">
            <?php
                echo('<option value="-1" selected>Все конкурсы</option>');
                $sql = "SELECT\n"
                . " * \n"
                . "FROM\n"
                . " contest \n";
                $tmp = arr_from_query($sql);
                
                foreach ($tmp as $k)
                {
                    $selected = ($k['id'] == $contest) ? ('selected') : ('');
                    echo('<option value ="' . $k['id'] . '" '.$selected.' >' . $k['name'] . '</option>');
                }
            ?>
          </select>
        </td>
      </tr>
    </table>
  </form>
</div>-->
<script language="JavaScript" type="text/javascript">
  function update () {
    //var sort=getElementById ('sortGroup').value;
    var contest=getElementById ('ContestGroup').value;
    nav ('.?contest='+contest);
  }
</script>
<p>
  На мониторе отражается текущее состояние присланных заданий.
</p>
<p>
  Если задание прислано, то, в соответствующей ячейке, будет указано время получения письма (по Перми) и объем вложения.
</p>
<table class="list">
  <tr class="h">
    <th scope="col" width="35px" style="text-align: center; border: 1px solid #AAAAAA"></th>
    <?php
    for ($i = 1; $i <= $task_count; $i++) {
      print('<th scope="col" width="50px" style="text-align: center; border: 1px solid #AAAAAA">' . $i . '</th>');
    }
    ?>
  </tr>
  <?php
    $k = 0;
    $teams = team_list('','',$current_contest);
    foreach ($teams as $t) {
      $status = arr_from_query(
          "SELECT
            `contest_status`.`time`, `contest_status`.`task`, `contest_status`.`size`
           FROM
            `contest_status`
           WHERE
            `contest_status`.`contest_id`=".$current_contest." AND
            `contest_status`.`team_id`=" . $t["id"] . " ORDER BY `task`");

      if ($k % 2 == 0 && $k != 0) {
        $bottom_line = 'style="border-bottom: 2px solid"';
        $k = 0;
      } else {
        $k++;
        $bottom_line = '';
      }
      print('<tr onmouseover="this.bgColor=\'#C2DFFF\';" onmouseout="this.bgColor=\'#EEEEEE\';"' . $bottom_line . '><th scope="row" style="text-align: center; border: 1px solid #AAAAAA">' . $t["reg_grade"] . '.' . $t["number"] . '</th>');

      $j = 0;
      for ($i = 1; $i <= $task_count; $i++) {
        $s = $status[$j];
        if ($s["task"] == $i) {
          $text = '<span style="color: green">';
          $time = $s["time"];
          $time = preg_replace('/:[0-9]{2}$/i', '', $time);
          $size = round($s["size"] / 1024);
          $text .= $time . '<br>' . $size . ' KB';
          $text .= '</span>';
          $j++;
        } else {
          $text = '<span style="color: red"><br><br></span>';
        }
        print('<td style="text-align: center; vertical-align: middle; border: 1px solid #AAAAAA">' . $text . '</td>');
      }
      print('</tr>');
    }
  ?>
</table>

