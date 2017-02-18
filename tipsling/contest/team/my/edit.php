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
  print ('HACKERS?');
  die;
}

global $id, $page;
$team = team_get_by_id($id);
$pupils = pupil_list_by_team_id($id);
$teachers = teacher_list_by_team_id($id);

$team_type = teamType_get_by_id($team['team_type_id']);
$team_type_list = manage_teamType_get_list();

$contest = contest_get_by_id($team['contest_id']);
$contest_list = get_prev_contest_list($contest['family_id']);
formo('title=Редактирование команды '.$team['reg_grade'].'.'.$team['number'].' (номер при регистрации: '.$team['reg_grade'].'.'.$team['reg_number'].');');
?>
<script>
    $(function(){
        var team_type_list = [];
        <?php
            foreach ($team_type_list as $key => $value) {
                echo('team_type_list.push({
                        "id":"'.$value['id'].'",
                        "name":"'.$value['name'].'",
                        "grade_name":"'.$value['grade_name'].'",
                        "grade_start_number":"'.$value['grade_start_number'].'",
                        "grade_max_number":"'.$value['grade_max_number'].'",
                        "grade_offset_number":"'.$value['grade_offset_number'].'"
                      });'
                    );
            }
        ?>
                
        var get_current_team_type = function() {
            var team_type_id = $('#team_type').val();
            var results = $.grep(team_type_list, function(e){ return e.id == team_type_id; });
            return results[0];
        };

        var team_type_changed = function() {
            var team_type = get_current_team_type();
            $('.grade_name').text(team_type.grade_name);
            
            if (team_type.grade_start_number === team_type.grade_max_number)
                $('#grade_line').hide();
            else
                $('#grade_line').show();
            
            hide_msg('grade_check_res');
        };
        
        var check = function() {   
            $('#teachers tr').each(function(index, elem){
                if (index === 0) return;

                var count = $('.teacher_contest select', $(this)).length;
                $('input[name="teacher_contest_count[]"]', $(this)).val(count);

                count = $('.teacher_winner select', $(this)).length;
                $('input[name="teacher_winner_count[]"]', $(this)).val(count);
            });

            $('.pupil_table').each(function(index, elem){
                var count = $('.pupil_contest select', $(this)).length;
                $('input[name="pupil_contest_count[]"]', $(this)).val(count);

                count = $('.pupil_winner select', $(this)).length;
                $('input[name="pupil_winner_count[]"]', $(this)).val(count);
            });

            var val = $('#date').val();
            $('#payment').prop('checked', val < '2015-03-02').trigger('change');
            
            var result = true;
            result = result && check_frm_teacher();
            result = result && check_frm_pupil();
            result = result && check_frm_comment();
            
            if (result) {
                var team_type = get_current_team_type();
                if (team_type.grade_start_number === team_type.grade_max_number)
                    $('#grade').val(team_type.grade_start_number);
                else
                    result = result && check_frm_grade();
            }
            
            if (result) {
                $(this).submit();
            }
            
            return false;
        };  
         
        var check_frm_grade = function() {
            var grade = $('#grade').val();
            var team_type = get_current_team_type();
            
            if (team_type.grade_name) {
                if (qtrim(grade) === ''){
                    show_msg ('grade_check_res', 'err', '<span class="grade_name">' + team_type.grade_name + '</span>' + ' команды не указан');
                    return false;
                }

                var wrongNumberMsg = '<span class="grade_name">' + team_type.grade_name + '</span>' + ' должен быть целым положительным числом от ' + team_type.grade_start_number + ' до ' + team_type.grade_max_number;
                if (!isnumber(grade)){
                    show_msg ('grade_check_res', 'err', wrongNumberMsg);
                    return false;
                }
                
                var grade = Number(grade);
                if (grade < team_type.grade_start_number || grade > team_type.grade_max_number) {
                    show_msg ('grade_check_res', 'err', wrongNumberMsg);
                    return false;
                }
            }

            hide_msg('grade_check_res');
            return true;
        };

        var check_frm_teacher = function() {
            var $teachers = $('input[name="teachers[]"]');

            for(var i=0; i<$teachers.length; i++){
                if (qtrim($teachers[i].value) !== ''){
                    hide_msg('teacher_check_res');      
                    return true;
                }
            }

            show_msg ('teacher_check_res', 'err', 'Это поле обзательно для заполнения');
            return false;
        };

        var check_frm_pupil = function() {
            var pupil = $('#pupil1_full_name').val();

            if (qtrim(pupil)==='') {
                show_msg ('pupil_check_res', 'err', 'Это поле обязательно для заполнения');
                return false;
            }

            hide_msg('pupil_check_res');
            return true;
        };

        var check_frm_comment = function() {
            var comment = $('#comment').val();

            if (comment.length > <?=opt_get('max_comment_len');?>) {
                show_msg ('comment_check_res', 'err', 'Поле "Комментарий" не может содержать более <?=opt_get('max_comment_len');?> символов');
                return false;
            }

            hide_msg('comment_check_res');
            return true;
        };
        
        $('#team_type').on('change', team_type_changed);
        $('#grade').on('blur', check_frm_grade);
        $('#teachers').on('blur', check_frm_teacher);
        $('#pupil1_full_name').on('blur', check_frm_pupil);
        $('#comment').on('blur', check_frm_comment);
        $('#edit_form').on('submit', check);
        
        team_type_changed();
    });  
</script>

<form id="edit_form" action=".?action=save&id=<?= $id; ?>&<?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST">
    <table class="clear" width="100%">
        <tr>
            <td width="30%" style="padding: 0 2px;">
                Тип команды:
            </td>
            <td style="padding: 0 2px;">
                <select id='team_type' name='team_type' style='vertical-align: middle;'>
                    <?php
                        foreach ($team_type_list as $key => $value) {
                            echo('<option '.($value['id']==$team['team_type_id']?'selected="selected"':'').' value="'.$value['id'].'">'.$value['name'].($value['description'] ? ' ('.$value['description'].')' : '').'</option>');
                        }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <div id="hr"></div>      
    <div id="grade_line">
        <table class="clear" width="100%" >
            <tr><td width="30%">
                    <span class='grade_name'><?= $team_type['grade_name'] ?></span> участников: <span class="error">*</span>
                </td>
                <td style="padding: 0 2px;">
                    <input type="text" class="txt block" id="grade" name="grade" <?=check_can_user_edit_teamgrade_field($team)?'':'readonly="readonly"' ?> value="<?= htmlspecialchars(stripslashes($team['grade'])); ?>">
                </td>
            </tr>
        </table>
        <div id="grade_check_res" style="display: none;"></div>
        <div id="hr"></div>      
    </div>
    <table class ="clear" width="100%" id="teachers">
        <tr>
            <th style="width: 30%; text-align: left; font-weight: normal;">Учителя: <span class="error">*</span></th>
            <th width="19%">ФИО</th>                        
            <th width="17%">Ранее подготовил команду для конкурсов:</th>
            <th width="17%">Ранее подготовил команду-призера для конкурсов:</th>   
            <th width="17%">Ранее участвовал в конкурсах РА ТРИЗ или ТРИЗ-Саммита:</th>
        </tr>
        <?php
            if (count($teachers)>0){
                foreach ($teachers as $teacher) {
                    echo("<tr>");
                    echo("<td style='text-align:right; vertical-align:top;'><img class='btn' src='".config_get('document-root')."/pics/cross.gif'/></td>");
                    #Столбец ФИО
                    echo("<td style='vertical-align:top;'><input type='text' class='txt block' name='teachers[]' value='".htmlspecialchars(stripslashes($teacher['FIO']))."'>");
                    echo("<input type='hidden' name='teacher_team[]'/>");
                    echo("<input type='hidden' name='teacher_contest_count[]'/>");
                    echo("<input type='hidden' name='teacher_winner_count[]'/>");
                    echo("</td>");
                    #Столбец участия в предыдущих конкурсах
                    echo("<td style='text-align:center; vertical-align:top;'>");
                    if ($teacher['contests']!=''){
                        foreach (explode(';', $teacher['contests']) as $teacher_contest_id) {
                            echo("<span class='teacher_contest'><select name='teacher_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                            foreach ($contest_list as $key => $value) {
                                echo('<option '.($value['id']==$teacher_contest_id?'selected="selected"':'').'value="'.$value['id'].'">'.$value['name'].'</option>');
                            }
                            echo("</select><img class='btn del_teacher_contest' src='".config_get('document-root')."/pics/cross.gif'/></span>");
                        }
                    }
                    echo("<span class='teacher_contest'><select name='teacher_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                    foreach ($contest_list as $key => $value) {
                        echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                    }
                    echo("</select></span></td>");
                    #Столбец побед в предыдущих конкурсах
                    echo("<td style='text-align:center; vertical-align:top;'>");
                    if ($teacher['winners']!=''){
                        foreach (explode(';', $teacher['winners']) as $teacher_winner_id) {
                            echo("<span class='teacher_winner'><select name='teacher_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                            foreach ($contest_list as $key => $value) {
                                echo('<option '.($value['id']==$teacher_winner_id?'selected="selected"':'').'value="'.$value['id'].'">'.$value['name'].'</option>');
                            }
                            echo("</select><img class='btn del_teacher_winner' src='".config_get('document-root')."/pics/cross.gif'/></span>");
                        }
                    }
                    echo("<span class='teacher_winner'><select name='teacher_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                    foreach ($contest_list as $key => $value) {
                        echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                    }
                    echo("</select></span></td>");
                    echo("<td><input type='text' class='txt block other_contest' name='teacher_other_contest[]' value='".$teacher['other_contest']."'</td>");
                    echo("</tr>");
                }
            }
            else {
                echo("<tr><td style='text-align:right;'><img class='btn' src='".config_get('document-root')."/pics/cross.gif'/></td>");
                #Столбец ФИО
                echo("<td><input type='text' class='txt block' name='teachers[]'/>");
                echo("<input type='hidden' name='teacher_team[]'/>");
                echo("<input type='hidden' name='teacher_contest_count[]'/>");
                echo("<input type='hidden' name='teacher_winner_count[]'/>");
                echo("</td>");
                #Столбец участия в предыдущих конкурсах
                echo("<td style='text-align:center;'><span class='teacher_contest'><select name='teacher_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                foreach ($contest_list as $key => $value) {
                    echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                }
                echo("</select></span></td>");
                #Столбец побед в предыдущих конкурсах
                echo("<td style='text-align:center;'><span class='teacher_winner'><select name='teacher_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                foreach ($contest_list as $key => $value) {
                    echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                }
                echo("</select></span></td>");
                echo("<td><input type='text' class='txt block other_contest' name='teacher_other_contest[]' </td>");
                echo("</tr>");
            }
        ?>
        <tr>
            <td></td>
            <td colspan="4"><button id="addTeacher" type="button" class="submitBtn block">Добавить</button></td>
        </tr>
    </table>
    <div id="teacher_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class ="clear pupil_table" width="100%">
        <tr>
            <th style="width: 30%; text-align: left; font-weight: normal;">1-ый участник: <span class="error">*</span></th>
            <th width="19%">ФИО</th>                        
            <th width="17%">Ранее участвовал в конкурсах:</th>
            <th width="17%">Ранее становился призером в конкурсах:</th>      
            <th width="17%">Ранее участвовал в конкурсах РА ТРИЗ или ТРИЗ-Саммита:</th>
        </tr>
        <tr>
            <?php
                echo("<td></td>");
                #Столбец ФИО
                echo("<td style='vertical-align:top;'><input type='text' class='txt block' id='pupil1_full_name' name='pupils[]' value='".htmlspecialchars(stripslashes($pupils[0]["FIO"]))."'>");
                echo("<input type='hidden' name='pupil_team[]' value='".$pupils[0]['idOfPupil_team']."'/>");
                echo("<input type='hidden' name='pupil_contest_count[]'/>");
                echo("<input type='hidden' name='pupil_winner_count[]'/>");
                echo("</td>");
                #Столбец участия в предыдущих конкурсах
                echo("<td style='text-align:center; vertical-align:top;'>");
                if ($pupils[0]['contests']!=''){
                    foreach (explode(';', $pupils[0]['contests']) as $pupil_contest_id) {
                        echo("<span class='pupil_contest'><select name='pupil_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                        foreach ($contest_list as $key => $value) {
                            echo('<option '.($value['id']==$pupil_contest_id?'selected="selected"':'').'value="'.$value['id'].'">'.$value['name'].'</option>');
                        }
                        echo("</select><img class='btn del_pupil_contest' src='".config_get('document-root')."/pics/cross.gif'/></span>");
                    }
                }
                echo("<span class='pupil_contest'><select name='pupil_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                foreach ($contest_list as $key => $value) {
                    echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                }
                echo("</select></span></td>");
                #Столбец побед в предыдущих конкурсах
                echo("<td style='text-align:center; vertical-align:top;'>");
                if ($pupils[0]['winners']!=''){
                    foreach (explode(';', $pupils[0]['winners']) as $pupil_winner_id) {
                        echo("<span class='pupil_winner'><select name='pupil_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                        foreach ($contest_list as $key => $value) {
                            echo('<option '.($value['id']==$pupil_winner_id?'selected="selected"':'').'value="'.$value['id'].'">'.$value['name'].'</option>');
                        }
                        echo("</select><img class='btn del_pupil_winner' src='".config_get('document-root')."/pics/cross.gif'/></span>");
                    }
                }
                echo("<span class='pupil_winner'><select name='pupil_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                foreach ($contest_list as $key => $value) {
                    echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                }
                echo("</select></span></td>");
                echo("<td><input type='text' class='txt block other_contest' name='pupil_other_contest[]' value='".$pupils[0]['other_contest']."'</td>");
            ?>
        </tr>        
    </table>
    <div id="pupil_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class ="clear pupil_table" width="100%">
        <tr>
            <th style="width: 30%; text-align: left; font-weight: normal;">2-ой участник:</th>
            <th width="19%">ФИО</th>                        
            <th width="17%">Ранее участвовал в конкурсах:</th>
            <th width="17%">Ранее становился призером в конкурсах:</th>                        
            <th width="17%">Ранее участвовал в конкурсах РА ТРИЗ или ТРИЗ-Саммита:</th>
        </tr>
        <tr>
            <?php
                echo("<td></td>");
                #Столбец ФИО
                echo("<td style='vertical-align:top;'><input type='text' class='txt block' id='pupil2_full_name' name='pupils[]' value='".htmlspecialchars(stripslashes($pupils[1]["FIO"]))."'>");
                echo("<input type='hidden' name='pupil_team[]' value='".$pupils[1]['idOfPupil_team']."'/>");
                echo("<input type='hidden' name='pupil_contest_count[]'/>");
                echo("<input type='hidden' name='pupil_winner_count[]'/>");
                echo("</td>");
                #Столбец участия в предыдущих конкурсах
                echo("<td style='text-align:center; vertical-align:top;'>");
                if ($pupils[1]['contests']!=''){
                    foreach (explode(';', $pupils[1]['contests']) as $pupil_contest_id) {
                        echo("<span class='pupil_contest'><select name='pupil_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                        foreach ($contest_list as $key => $value) {
                            echo('<option '.($value['id']==$pupil_contest_id?'selected="selected"':'').'value="'.$value['id'].'">'.$value['name'].'</option>');
                        }
                        echo("</select><img class='btn del_pupil_contest' src='".config_get('document-root')."/pics/cross.gif'/></span>");
                    }
                }
                echo("<span class='pupil_contest'><select name='pupil_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                foreach ($contest_list as $key => $value) {
                    echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                }
                echo("</select></span></td>");
                #Столбец побед в предыдущих конкурсах
                echo("<td style='text-align:center; vertical-align:top;'>");
                if ($pupils[1]['winners']!=''){
                    foreach (explode(';', $pupils[1]['winners']) as $pupil_winner_id) {
                        echo("<span class='pupil_winner'><select name='pupil_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                        foreach ($contest_list as $key => $value) {
                            echo('<option '.($value['id']==$pupil_winner_id?'selected="selected"':'').'value="'.$value['id'].'">'.$value['name'].'</option>');
                        }
                        echo("</select><img class='btn del_pupil_winner' src='".config_get('document-root')."/pics/cross.gif'/></span>");
                    }
                }
                echo("<span class='pupil_winner'><select name='pupil_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                foreach ($contest_list as $key => $value) {
                    echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                }
                echo("</select></span></td>");
                echo("<td><input type='text' class='txt block other_contest' name='pupil_other_contest[]' value='".$pupils[1]['other_contest']."'</td>");
            ?>
        </tr>        
    </table>
    <div id="hr"></div>
    <table class ="clear pupil_table" width="100%">
        <tr>
            <th style="width: 30%; text-align: left; font-weight: normal;">3-ий участник:</th>
            <th width="19%">ФИО</th>                        
            <th width="17%">Ранее участвовал в конкурсах:</th>
            <th width="17%">Ранее становился призером в конкурсах:</th>     
            <th width="17%">Ранее участвовал в конкурсах РА ТРИЗ или ТРИЗ-Саммита:</th>
        </tr>
        <tr>
            <?php
                echo("<td></td>");
                #Столбец ФИО
                echo("<td style='vertical-align:top;'><input type='text' class='txt block' id='pupil2_full_name' name='pupils[]' value='".htmlspecialchars(stripslashes($pupils[2]["FIO"]))."'>");
                echo("<input type='hidden' name='pupil_team[]' value='".$pupils[2]['idOfPupil_team']."'/>");
                echo("<input type='hidden' name='pupil_contest_count[]'/>");
                echo("<input type='hidden' name='pupil_winner_count[]'/>");
                echo("</td>");
                #Столбец участия в предыдущих конкурсах
                echo("<td style='text-align:center; vertical-align:top;'>");
                if ($pupils[2]['contests']!=''){
                    foreach (explode(';', $pupils[2]['contests']) as $pupil_contest_id) {
                        echo("<span class='pupil_contest'><select name='pupil_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                        foreach ($contest_list as $key => $value) {
                            echo('<option '.($value['id']==$pupil_contest_id?'selected="selected"':'').'value="'.$value['id'].'">'.$value['name'].'</option>');
                        }
                        echo("</select><img class='btn del_pupil_contest' src='".config_get('document-root')."/pics/cross.gif'/></span>");
                    }
                }
                echo("<span class='pupil_contest'><select name='pupil_contest_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                foreach ($contest_list as $key => $value) {
                    echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                }
                echo("</select></span></td>");
                #Столбец побед в предыдущих конкурсах
                echo("<td style='text-align:center; vertical-align:top;'>");
                if ($pupils[2]['winners']!=''){
                    foreach (explode(';', $pupils[2]['winners']) as $pupil_winner_id) {
                        echo("<span class='pupil_winner'><select name='pupil_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                        foreach ($contest_list as $key => $value) {
                            echo('<option '.($value['id']==$pupil_winner_id?'selected="selected"':'').'value="'.$value['id'].'">'.$value['name'].'</option>');
                        }
                        echo("</select><img class='btn del_pupil_winner' src='".config_get('document-root')."/pics/cross.gif'/></span>");
                    }
                }
                echo("<span class='pupil_winner'><select name='pupil_winner_value[]' style='vertical-align: middle;'><option value='-1'></option>");
                foreach ($contest_list as $key => $value) {
                    echo('<option value="'.$value['id'].'">'.$value['name'].'</option>');
                }
                echo("</select></span></td>");
                echo("<td><input type='text' class='txt block other_contest' name='pupil_other_contest[]' value='".$pupils[2]['other_contest']."'</td>");
            ?>
        </tr>        
    </table>
    <div id="hr"></div>
    <table class ="clear" width="100%">
        <tr><td width="30%">
                В какой день участвует:
            </td>
            <td style="padding: 0 2px;">
                <select id="contest_day" name="contest_day" <?=check_can_user_edit_teamsmena_field($team)?'':'disabled' ?>>
                    <option value="сб" <?= $team['contest_day']=='сб'?'selected="selected"':'' ?>>суббота</option>
                    <option value="вс" <?= $team['contest_day']=='вс'?'selected="selected"':'' ?>>воскресенье</option>
                </select>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class ="clear" width="100%">
        <tr><td width="30%">
                В какую смену учится:
            </td>
            <td style="padding: 0 2px;">
                <select id="smena" name="smena" <?=check_can_user_edit_teamsmena_field($team)?'':'disabled' ?>>
                    <option value="1" <?= $team['smena']=='1'?'selected="selected"':'' ?>>1 смена (играет с 14 до 16 часов местного времени)</option>
                    <option value="2" <?= $team['smena']=='2'?'selected="selected"':'' ?>>2 смена (играет с 11 до 13 часов местного времени)</option>
                </select>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%">
                Дата оплаты оргвзноса:
            </td>
            <td style="padding: 0 2px;">
                <?= calendar('date', htmlspecialchars($team['payment_date'])) ?>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%">
                Адреса репостов:
            </td>
            <td style="padding: 0 2px;">
                <table width="100%" id="reposts">
                    <?php
                        $reposts = preg_split('/;/', $team['reposts']);
                        foreach ($reposts as $k => $repost) {
                            print("<tr><td><input type='text' class='txt block' name='repost[]' value='" . $repost . "'/></td><td width='24' style='text-align:right;'><img class='btn' src='".config_get('document-root')."/pics/cross.gif'/></td></tr>");
                        }
                    ?>
                </table>
                <button id="addRepost" type="button" class="submitBtn">Добавить</button>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class ="clear" width="100%">
        <tr><td width="30%">
                Примечание:
            </td>
            <td style="padding: 0 2px;">
                <input type="text" id="comment" name="comment" value="<?= htmlspecialchars(stripslashes($team['comment'])); ?>" class="txt block">
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%">
                Калькулятор скидок:
            </td>
            <td style="padding: 0 2px;">
                <input type="checkbox" disabled="disabled" id="repost" value="100">[100р]Скидка за распространение информации о конкурсе (не менее 10 сообщений о конкурсе в сети)</br>
                <input type="checkbox" disabled="disabled" id="payment" value="100">[100р]Скидка за раннюю оплату (до 1 марта)</br>
                <input type="checkbox" disabled="disabled" id="years" value="100">[100р]Скидка за возраст (для команд с 1 по 9 класс)</br>
                <input type="checkbox" disabled="disabled" id="participant" value="100">[100р]Скидка участникам предыдущих конкурсов (хотя бы один из учеников уже принимал участие в конкурсе)</br>
                <input type="checkbox" disabled="disabled" id="veteran" value="100">[100р]Скидка "ветеранам" конкурса (хотя бы один из учеников принимал участие в конкурсе 3 или более раз)</br>
                <input type="checkbox" disabled="disabled" id="winner" value="100">[100р]Скидка призерам предыдущих конкурсов (хотя бы один из учеников занимал призовое место в одном из предыдущих конкурсов)</br>
                <input type="checkbox" disabled="disabled" id="teacher_participant" value="100">[100р]Скидка учителям-участникам прежних конкурсов</br>
                <input type="checkbox" disabled="disabled" id="teacher_winner" value="100">[100р]Скидка учителям-победителям прежних конкурсов</br>
                <input type="checkbox" disabled="disabled" id="other_contest" value="100">[100р]Скидка за участие в конкурсах Российской ассоциации ТРИЗ и ТРИЗ-Саммита</br>
                </br>
                Макс. сумма оргвзноса: <input type="text" readonly="readonly" value="1300" style="width:75px"/>
                Суммарная скидка: <input type="text" id="discount" readonly="readonly" value="0" style="width:75px"/>
                Оргвзнос: <input type="text" id="result" name="payment_sum" readonly="readonly" value="1300" style="width:75px" />
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    
    <input type="hidden" value="<?=($team['is_payment'])?'1':'0'?>" id="is_payment_value" name="is_payment_value"></input>
    <div id="comment_check_res" style="display: none;"></div>

    <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?<?= (($page != '') ? ('&page=' . $page) : ('')); ?>');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<script>
    $(function(){
       $('input[type="checkbox"]').on('change', function(){
           var maxvalue = 1300,
               discount = 0;
           $('input:checked').each(function(){
               discount += parseInt($(this).val());
           });
           $('#discount').val(discount);
           $('#result').val(maxvalue-discount);
       });
    });
    
    $(function(){
        var team_type_list = [];
        <?php
            foreach ($team_type_list as $key => $value) {
                echo('team_type_list.push({
                        "id":"'.$value['id'].'",
                        "name":"'.$value['name'].'",
                        "grade_name":"'.$value['grade_name'].'",
                        "grade_start_number":"'.$value['grade_start_number'].'",
                        "grade_max_number":"'.$value['grade_max_number'].'",
                        "grade_offset_number":"'.$value['grade_offset_number'].'"
                      });'
                    );
            }
        ?>
                
        var get_current_team_type = function() {
            var team_type_id = $('#team_type').val();
            var results = $.grep(team_type_list, function(e){ return e.id == team_type_id; });
            return results[0];
        };
        
        var $teachers = $('#teachers'),
            $smena = $('#smena'),
            $contest_day = $('#contest_day'),
        
            unique = function(list) {
                var result = [];
                $.each(list, function(i, e) {
                    if ($.inArray(e, result) == -1) result.push(e);
                });
                return result;
            },
        
            getDistinctValues = function(selectList, emptyValue){
                var values = selectList.map(function(){
                    return $(this).val().trim();
                }).get();
                values = $.grep(values, function(elem){
                    return elem != emptyValue;
                });
                return unique(values);
            },
    
            SetRepostDiscount = function(){
                var values = getDistinctValues($('#reposts input'), '');
                $('#repost').prop('checked', values.length>=10).trigger('change');                            
            },
            
            SetPaymentDiscount = function(){
                var val = $('#date').val();
                $('#payment').prop('checked', val < '2017-03-02').trigger('change');
            },
        
            SetYearsDiscount = function(){
                var grade = $('#grade').val();
                var team_type = get_current_team_type();
                
                if (isnumber(grade)){
                    grade = Number(grade) + Number(team_type['grade_offset_number']);
                }
                
                $('#years').prop('checked', grade>=1 && grade<=9).trigger('change');
            },
        
            SetPupilContestDiscount = function(){
                var values = getDistinctValues($('.pupil_table .pupil_contest select'), -1);
                $('#participant').prop('checked', values.length >= 1).trigger('change');

                var veteran = false;
                $('.pupil_table').each(function(){
                    var values = getDistinctValues($('.pupil_contest select', $(this)), -1);
                    if (values.length >= 3)
                        veteran = true;
                });
                $('#veteran').prop('checked', veteran).trigger('change');
            },
        
            SetPupilWinnerDiscount = function(){
                var values = getDistinctValues($('.pupil_table .pupil_winner select'), -1);
                $('#winner').prop('checked', values.length >= 1).trigger('change');
            },
            
            SetTeacherContestDiscount = function(){
                var values = getDistinctValues($('#teachers .teacher_contest select'), -1);
                $('#teacher_participant').prop('checked', values.length >= 1).trigger('change');
            },
        
            SetTeacherWinnerDiscount = function(){
                var values = getDistinctValues($('#teachers .teacher_winner select'), -1);
                $('#teacher_winner').prop('checked', values.length >= 1).trigger('change');
            },
            
            SetOtherContestDiscount = function(){
                var values = getDistinctValues($('.other_contest'), "");
                $('#other_contest').prop('checked', values.length >= 1).trigger('change');
            },
        
            AddRepostField = function(){
                $('#reposts').find('tr:last').after("<tr><td><input type='text' class='txt block' name='repost[]' value=''/></td><td width='24' style='text-align:right;'><img class='btn' src='<?=config_get('document-root')?>/pics/cross.gif'/></td></tr>");
            },
                    
            RemoveRepostField = function(){
                var $rows = $(this).closest('table').find('tr');
                if ($rows.length>1){
                    $(this).closest('tr').remove();
                }
                SetRepostDiscount();
            },
                    
            AddTeacherField = function(){
                var $row = $('#teachers').find('tr:last').prev('tr'),
                    $clone = $row.clone(),
                    $toremove = $('.teacher_contest', $clone);
                $toremove.splice($toremove.length - 1, 1);
                $toremove.remove();

                $toremove = $('.teacher_winner', $clone);
                $toremove.splice($toremove.length - 1, 1);
                $toremove.remove();

                $('input', $clone).val('');
                $row.after($clone);
                
                $clone.on('change', '.teacher_contest:last', function(){
                    AddTeacherContest.call(this);
                });
                $clone.on('change', '.teacher_winner:last', function(){
                    AddTeacherWinner.call(this);
                });
            },

            RemoveTeacherField = function(){
                var $rows = $(this).parents('table:first').find('tr');
                if ($rows.length>3){
                    $(this).parents('tr:first').remove();
                }
                SetTeacherContestDiscount();
                SetTeacherWinnerDiscount();
            },
                    
            AddTeacherContest = function(){
                var $this = $(this),
                    $clone = $this.clone();
                $clone.val('-1');
                $this.append("<img class='btn del_teacher_contest' src='<?=config_get('document-root')?>/pics/cross.gif'/>");
                $this.parent().append($clone);
            },
            
            AddTeacherWinner = function(){
                var $this = $(this),
                    $clone = $this.clone();
                $clone.val('-1');
                $this.append("<img class='btn del_teacher_winner' src='<?=config_get('document-root')?>/pics/cross.gif'/>");
                $this.parent().append($clone);
            },

            ContestDayChanged = function(){
                var $table = $smena.closest('table');
                if ($contest_day.val()=='сб'){
                    $table.show();
                    $table.nextAll('div:first').show();
                }
                else if ($contest_day.val()=='вс'){
                    $table.hide();
                    $table.nextAll('div:first').hide();
                }            
            };
    
        $('#addTeacher').on('click', AddTeacherField);
        $teachers.on('click', 'img',  RemoveTeacherField);
        $contest_day.on('change', ContestDayChanged);
        ContestDayChanged();
        
        
        $('.pupil_table').on('change', '.pupil_contest:last', function(){
            var $this = $(this),
                $clone = $this.clone();
            $clone.val('-1');
            $this.append("<img class='btn del_pupil_contest' src='<?=config_get('document-root')?>/pics/cross.gif'/>");
            $this.parent().append($clone);
        });
        
        $('.pupil_table').on('click', '.del_pupil_contest', function(){
            $(this).parent().remove();
            SetPupilContestDiscount();
        });
        
        $('.pupil_table').on('change', '.pupil_winner:last', function(){
            var $this = $(this),
                $clone = $this.clone();
            $clone.val('-1');
            $this.append("<img class='btn del_pupil_winner' src='<?=config_get('document-root')?>/pics/cross.gif'/>");
            $this.parent().append($clone);
        });
        
        $('.pupil_table').on('click', '.del_pupil_winner', function(){
            $(this).parent().remove();
            SetPupilWinnerDiscount();
        });
        
        $('#teachers tr').on('change', '.teacher_contest:last', function(){
            AddTeacherContest.call(this);
        });
        
        $('#teachers').on('click', '.del_teacher_contest', function(){
            $(this).parent().remove();
            SetTeacherContestDiscount();
        });
        
        $('#teachers tr').on('change', '.teacher_winner:last', function(){
            AddTeacherWinner.call(this);
        });
        
        $('#teachers').on('click', '.del_teacher_winner', function(){
            $(this).parent().remove();
            SetTeacherWinnerDiscount();
        });
        
        
        $('.pupil_table').on('change', '.pupil_contest', function(){
            SetPupilContestDiscount();
        }).on('change', '.pupil_winner', function(){
            SetPupilWinnerDiscount();
        }).on('change', '.other_contest', function(){
            SetOtherContestDiscount();
        });
        
        $('#teachers').on('change', '.teacher_contest', function(){
            SetTeacherContestDiscount();
        }).on('change', '.teacher_winner', function(){
            SetTeacherWinnerDiscount();
        }).on('change', '.other_contest', function(){
            SetOtherContestDiscount();
        });
        
        $('#team_type').on('input', function(){
            SetYearsDiscount();
        });
        
        $('#grade').on('input', function(){
            SetYearsDiscount();
        });
        
        $('#reposts').on('input', 'input', function(){
            SetRepostDiscount();
        });
        
        $('#addRepost').on('click', AddRepostField);
        $('#reposts').on('click', 'img',  RemoveRepostField);        
        
        SetRepostDiscount();
        SetPaymentDiscount();
        SetYearsDiscount();
        SetPupilContestDiscount();
        SetPupilWinnerDiscount();
        SetTeacherContestDiscount();
        SetTeacherWinnerDiscount();
        SetOtherContestDiscount();
    });
</script>
<?php
    formc ();
?>
