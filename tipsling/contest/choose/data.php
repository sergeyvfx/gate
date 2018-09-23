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
?>

<script language="JavaScript" type="text/javascript">
    function check(frm) {
        frm.submit ();
    }
</script>

<div id="snavigator">Выбор конкурса</div>
${information}

<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $current_contest, $contest;
    
    $archived = (get_contest_status($current_contest) & 16) == 16;//"Архивный конкурс"
    if ($action == 'chosen' && !$archived) {
      $current_contest = $contest;
    }
    
    if ($current_contest != -1 && $current_contest != '')
    {
        $it = contest_get_by_id($current_contest);
        printf('Текущий конкурс: '.$it['name']);
    }
    else
        printf('Текущий конкурс еще не выбран');
    ?>
  </div>
</div>

<form action=".?action=chosen" method="POST" onsubmit="check (this); return false;">
<table>
<tr class="h">
<td>
    Конкурс: 
</td>
<td>
    <select id="contest" name ="contest">
    <?php
        global $current_contest;
        $sql = "SELECT * FROM contest where send_to_archive is null or DATE_FORMAT(send_to_archive,'%Y-%m-%d')>DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d')";
        $tmp = arr_from_query($sql);
                
        foreach ($tmp as $k)
        {
            $selected = ($k['id'] == $current_contest) ? ('selected') : ('');
            echo('<option value = "' . $k['id'] . '" '.$selected.' >' . $k['name'] . '</option>');
        }
    ?>
    </select>          
</td>
</tr>
</table>

<div class="formPast">
    <button class="submitBtn block" type="submit">Сохранить</button>
</div>
</form>


