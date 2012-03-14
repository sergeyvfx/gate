<?php
    include '../../globals.php';
    include $DOCUMENT_ROOT . '/inc/include.php';
    db_connect (config_get ('check-database'));
    
    global $id, $contest_id, $grade, $number, $task, $date, $size;
    if ($id=='120121')
    {
        $sql = "SELECT `team`.`id` FROM `team` WHERE `team`.`grade`=".$grade." AND `team`.`number`=".$number." AND `team`.`contest_id`=".$contest_id;
        $res = arr_from_query($sql);
        if (count($res)>0)
        {
            $team = $res[0];
            $teamId = $team['id'];
            $result = db_insert('contest_status', 
                      array('contest_id' => db_string($contest_id),
                            'task' => db_string($task),
                            'team_id' => db_string($teamId),
                            'time' => db_string($date),
                            'size' => db_string($size)));
            if ($result==false)
            {
                echo('Не удалось внести запись в БД(contest_id='.$contest_id.', task='.$task.', team_id='.$teamId.')');
            }
        }
        else
        {
            echo("Ошибка при получении номера команды");
        }
    }
?>      
