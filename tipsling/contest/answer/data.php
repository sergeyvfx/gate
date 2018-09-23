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

global $current_contest, $DOCUMENT_ROOT, $action;

if ($current_contest =='' || $current_contest == -1) {
    header('Location: ../choose');
}

if (!user_authorized ()) {
  header('Location: ../../../login');
} 

$contest = contest_get_by_id($current_contest);
$task_count = 20;
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a>Отправка решений</div>
${information}


<?php
    if ($action=='upload')
    {
        global $answer_team, $answer_task, $answer_file;
        $team = team_get_by_id($answer_team);
        $directory = $DOCUMENT_ROOT."/uploaded_files/answers/".$answer_task;
        
        if (!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        
        // Проверяем загружен ли файл
        if(is_uploaded_file($_FILES["answer_file"]["tmp_name"]))
        {
            $sql = "SELECT * FROM `contest_status` WHERE `team_id`=".$answer_team." AND `task`=".$answer_task." AND `contest_id`=".$current_contest;
            $res = arr_from_query($sql);
            if (count($res)==0){
                if (intval($_FILES["answer_file"]["size"]) > (5*1024*1024)){        
                    echo('<script type="text/javascript">
                            alert("Размер файла не должен превышать 5Мб");
                          </script>');
                }
                else {
                    $offset = date("Z");
                    $offset = (-1)*parseint($offset)+5*60*60; //PERM OFFSET FROM SERVER TIME
                    $original_time = date("Y-m-d H:i:s", time()); //server time
                    $prm_time = date("Y-m-d H:i:s", strtotime($original_time." ".$offset." Seconds")); //perm time
                    $time_array = explode(" ", $prm_time);
                    $perm_date_only = $time_array[0];
                    $perm_time_only = $time_array[1];
                    move_uploaded_file($_FILES["answer_file"]["tmp_name"], $directory."/".$team['reg_grade'].'.'.$team['number'].'-'.$answer_task.strrchr($_FILES["answer_file"]["name"], '.'));
                    db_insert('contest_status', 
                              array('contest_id' => db_string($current_contest),
                                    'task' => db_string($answer_task),
                                    'team_id' => db_string($answer_team),
                                    'date' => db_string($perm_date_only),
                                    'time' => db_string($perm_time_only),
                                    'size' => db_string($_FILES["answer_file"]["size"])));
                    echo('<script type="text/javascript">
                            alert("Решение было успешно отправлено");
                            location = "/tipsling/contest/status/";
                          </script>');
                }
            }
            else {
                echo('<script type="text/javascript">
                        alert("Вы уже отправляли решение данной задачи этой командой");
                        location = "/tipsling/contest/status/";
                      </script>');
            }
            
        }
    }
    else
    {
?>

<div class="form">
    <div class="content">
        <form action=".?action=upload" method="post" enctype="multipart/form-data">
            <table class="list">
                <tr>
                    <td width="75px">Команда</td>
                    <td>
                        <select width="100%" name="answer_team">
                            <?
                                $list = team_list(user_id(), '', $current_contest);
                                foreach ($list as $team) {
                                    echo('<option value="'.$team['id'].'">'.$team['reg_grade'].'.'.$team['number'].' (рег. '.$team['reg_grade'].'.'.$team['number'].')</option>');
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Задание</td>
                    <td>
                        <select width="100%" name="answer_task">
                            <option value="1">1</option><option value="2">2</option>
                            <option value="3">3</option><option value="4">4</option>
                            <option value="5">5</option><option value="6">6</option>
                            <option value="7">7</option><option value="8">8</option>
                            <option value="9">9</option><option value="10">10</option>
                            <option value="11">11</option><option value="12">12</option>
                            <option value="13">13</option><option value="14">14</option>
                            <option value="15">15</option><option value="16">16</option>
                            <option value="17">17</option><option value="18">18</option>
                            <option value="19">19</option><option value="20">20</option>                
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Решение</td>
                    <td>
                        <input type="file" name="answer_file" id="answer_file"/> 
                    </td>
                </tr>
            </table>
            <br/>
            <input type="submit" value="Отправить" id="send_answer"/>
        </form>
    </div>
</div>
<script>
    $(function(){
        $('#send_answer').click(function(){
            if (document.getElementById('answer_file').files[0].size > 5*1024*1024){
                alert('Размер файла не должен превышать 5Мб');
                return false;
            }
        });
    });
</script>
<?php 
    }
?>