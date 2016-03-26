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

global $current_contest, $DOCUMENT_ROOT, $task;

if ($current_contest =='' || $current_contest == -1) {
    header('Location: ../choose');
}

if (!user_authorized ()) {
  header('Location: ../../../login');
} 

if (!is_user_in_group (user_id(), 4)) {
  print (content_error_page(403));
  return;
}

$contest = contest_get_by_id($current_contest);
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a>Просмотр решений</div>
${information}

<div class="form">
    <div class="content">
        <a href="/tipsling/contest/answer/view?task=1">1</a>
        <a href="/tipsling/contest/answer/view?task=2">2</a>
        <a href="/tipsling/contest/answer/view?task=3">3</a>
        <a href="/tipsling/contest/answer/view?task=4">4</a>
        <a href="/tipsling/contest/answer/view?task=5">5</a>
        <a href="/tipsling/contest/answer/view?task=6">6</a>
        <a href="/tipsling/contest/answer/view?task=7">7</a>
        <a href="/tipsling/contest/answer/view?task=8">8</a>
        <a href="/tipsling/contest/answer/view?task=9">9</a>
        <a href="/tipsling/contest/answer/view?task=10">10</a>
        <a href="/tipsling/contest/answer/view?task=11">11</a>
        <a href="/tipsling/contest/answer/view?task=12">12</a>
        <a href="/tipsling/contest/answer/view?task=13">13</a>
        <a href="/tipsling/contest/answer/view?task=14">14</a>
        <a href="/tipsling/contest/answer/view?task=15">15</a>
        <a href="/tipsling/contest/answer/view?task=16">16</a>
        <a href="/tipsling/contest/answer/view?task=17">17</a>
        <a href="/tipsling/contest/answer/view?task=18">18</a>
        <a href="/tipsling/contest/answer/view?task=19">19</a>
        <a href="/tipsling/contest/answer/view?task=20">20</a>
    </div>
</div>

<?php
    if ($task != '') {
        $directory = $DOCUMENT_ROOT."/uploaded_files/answers/".$task;
        $folder = opendir($directory);
        $files = array();
        while($file = readdir($folder)) {
            if ($file != "." && $file != ".." ) {
                $files[count($files)] = $file;
            }
        }
        
        $sql = 'select cs.*, team.grade, team.number from contest_status cs join team on cs.team_id = team.id where cs.contest_id='.$contest['id'].' and cs.task='.$task.' order by cs.date desc, cs.time desc';
        $answers = arr_from_query($sql);
        print '<table style="width:100%; text-align:center;">';
        print '<tr>';
        print '<th>Дата</th>';
        print '<th>Время</th>';
        print '<th>Решение</th>';
        print '</tr>';
        
        foreach ($answers as $value) {
            $filename = '';
            foreach ($files as $name) {
                $pos = strpos($name, $value['grade'].'.'.$value['number'].'-'.$task);
                if ($pos !== false && $pos >= 0) {
                    $filename = $name;
                    break;
                }                        
            }
            
            if ($filename != '') {
                print '<tr>';
                print '<td>'.$value['date'].'</td>';
                print '<td>'.$value['time'].'</td>';
                print '<td><a href="'.config_get('document-root').'/uploaded_files/answers/'.$task.'/'.$filename.'">'.$filename.'</a></td>';
                print '</tr>';
            }
        }
        print '</table>';
    }
?>