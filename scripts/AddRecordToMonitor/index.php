<?php
    include '../../globals.php';
    include $DOCUMENT_ROOT . '/inc/include.php';
    db_connect (config_get ('check-database'));
    $MonitorCode = opt_get("MonitorCode");
    
    global $id, $contest_id, $grade, $number, $task, $date, $size, $action;
    if ($id == $MonitorCode) {
        if ($action == 'getfilelist') {
            $sql = "SELECT * FROM `contest` WHERE `id`=".$contest_id;
            $res = arr_from_query($sql);
            if (count($res)>0) {
                $contest = $res[0];
                $directory = $DOCUMENT_ROOT."/uploaded_files/answers/".$task;
                $folder = opendir($directory);
                $result = "";
                while($file = readdir($folder)) {
                    if ($file != "." && $file != ".." ) {
                        $result .= $file.'|';
                    }
                }
                if ($result != '') {
                    echo("list of files:".substr($result, 0, strlen($result)-1));
                }
            }
        } else {
            $sql = "SELECT `team`.`id` FROM `team` WHERE `team`.`grade`=".$grade." AND `team`.`number`=".$number." AND `team`.`contest_id`=".$contest_id;
            $res = arr_from_query($sql);
            if (count($res)>0) {
                $team = $res[0];
                $teamId = $team['id'];
                if ($action == 'add') {
                    $sql = "SELECT * FROM `contest_status` WHERE `team_id`=".$teamId." AND `task`=".$task." AND `contest_id`=".$contest_id;
                    $statuses = arr_from_query($sql);
                    if (count($statuses)==0) {
                        $result = db_insert('contest_status', 
                                  array('contest_id' => db_string($contest_id),
                                        'task' => db_string($task),
                                        'team_id' => db_string($teamId),
                                        'time' => db_string($date),
                                        'size' => db_string(0)));
                        if ($result==false) {
                            echo('Не удалось внести запись в БД(contest_id='.$contest_id.', task='.$task.', team_id='.$teamId.')');
                        }
                    } else {
                        echo('Решение данной задачи уже было прислано ранее');
                    }
                } else {
                    // Проверяем загружен ли файл
                    if(is_uploaded_file($_FILES["answer_file"]["tmp_name"]))
                    {
                        $sql = "SELECT * FROM `team` WHERE `id`=".$teamId;
                        $res = arr_from_query($sql);
                        if (count($res)>0) {
                            $team = $res[0];
                            $directory = $DOCUMENT_ROOT."/uploaded_files/answers/".$task;
                        }

                        move_uploaded_file($_FILES["answer_file"]["tmp_name"], $directory."/".$team['grade'].'.'.$team['number'].'-'.$task.strrchr($_FILES["answer_file"]["name"], '.'));
                        db_update('contest_status', 
                                  array('size' => db_string($size)),
                                        "`team_id`=".$teamId." AND `task`=".$task." AND `contest_id`=".$contest_id);                        
                    } else {
                        echo('Не был прикреплен файл с решением(contest_id='.$contest_id.', task='.$task.', team_id='.$teamId.')');
                    }                    
                }
            } else {
                echo("Ошибка при получении номера команды");
            }
        }
    }
?>      
