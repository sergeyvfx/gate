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
formo('title=Редактирование команды '.$team['grade'].'.'.$team['number'].' (номер при регистрации: '.$team['grade'].'.'.$team['reg_number'].');');

?>

<script src="<?= config_get('document-root') . "/scripts/autocomplete.js" ?>"></script>

<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var grade  = getElementById ('grade').value;
    var pupil1_full_name   = getElementById ('pupil1_full_name').value;
    var $teachers = $('input[name="teachers[]"]');

    if (qtrim(grade)==''){
      alert('Укажите класс команды')
      return;
    }

    if (!isnumber(grade)){
      alert('Класс должен быть целым положительным числом от 1 до 17 (1-11 для школьников, 12-17 для студентов)');
      return;
    }

    if (grade<1||grade>17){
        alert('Класс должен быть целым положительным числом от 1 до 17 (1-11 для школьников, 12-17 для студентов)');
        return;
    }

    var isTeacherEmpty=true;
    for(var i=0; i<$teachers.length; i++){
        if (qtrim($teachers[i].value) != ''){
            isTeacherEmpty=false;
            break;
        }
    }
    if (isTeacherEmpty){
      alert('ФИО учителя не может быть пустым');
      return;
    }

    if (qtrim(pupil1_full_name)==''){
      alert('ФИО первого участника не может быть пустым');
      return;
    }
    frm.submit ();
  }

  function check_frm_grade() {
      var grade = getElementById ('grade').value;

      if (qtrim(grade)==''){
          show_msg ('grade_check_res', 'err', 'Укажите класс команды');
          return;
      }

      if (!isnumber(grade)){
          show_msg ('grade_check_res', 'err', 'Класс должен быть целым положительным числом от 1 до 17 <i>(1-11 для школьников, 12-17 для студентов)</i>');
          return;
      }

      if (grade<1 || grade>17){
          show_msg('grade_check_res','err','Класс должен быть целым положительным числом от 1 до 17 <i>(1-11 для школьников, 12-17 для студентов)</i>');
          return;
      }

      hide_msg('grade_check_res');
  }

  function check_frm_teacher() {
      var $teachers = $('input[name="teachers[]"]');
      
      for(var i=0; i<$teachers.length; i++){
          if (qtrim($teachers[i].value) != ''){
              hide_msg('teacher_check_res');      
              return;
          }
      }

      show_msg ('teacher_check_res', 'err', 'Это поле обзательно для заполнения');
  }

  function check_frm_pupil() {
      var pupil = getElementById ('pupil1_full_name').value;

      if (qtrim(pupil)=='') {
          show_msg ('pupil_check_res', 'err', 'Это поле обязательно для заполнения');
          return;
      }

      hide_msg('pupil_check_res');
  }

  function check_frm_comment() {
      var comment = getElementById ('comment').value;

      if (comment.length > <?=opt_get('max_comment_len');?>) {
          show_msg ('comment_check_res', 'err', 'Поле "Комментарий" не может содержать более <?=opt_get('max_comment_len');?> символов');
          return;
      }

      hide_msg('comment_check_res');
  }
  
  $(function (){
    var $teachers = $('#teachers'),
        $smena = $('#smena'),
        $contest_day = $('#contest_day'),
        AddTeacherField = function(){
            $('#teachers').find('tr:last').after("<tr><td><input <?=($is_user_admin?'':'readonly="true"')?> type='text' class='txt block' name='teachers[]' onblur='check_frm_teacher ();' value=''/><input type='hidden' name='teacher_team[]'/></td><td width='24' style='text-align:right;'><img class='btn' src='<?=config_get('document-root')?>/pics/cross.gif'/></td></tr>");
        },
        RemoveTeacherField = function(){
            var $rows = $(this).parents('table:first').find('tr');
            if ($rows.length>1){
                $(this).parents('tr:first').remove();
            }
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
  });
  
  
  </script>

  
  <form action=".?action=save&id=<?= $id; ?>&<?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST" onsubmit="check (this); return false;">
    <table class="clear" width="100%">
        <tr><td width="275px">
                Класс участников: <span class="error">*</span>
                <br/><i>(Для ВУЗов: 1 курс = 12 класс, 2 курс = 13 и т.д.)</i>
            </td>
            <td style="padding: 0 2px;">
                <input <?=$is_user_admin?'':'readonly="readonly"'?> type="text" class="txt block" id="grade" name="grade" onblur="check_frm_grade ();" value="<?= htmlspecialchars(stripslashes($team['grade'])); ?>">
            </td>
            <td width="85px" style="padding-left: 20px;">
                Номер команды:
            </td>
            <td style="padding: 0 2px;">
                <input type="text" <?=$is_user_admin?'':'readonly="readonly"'?> class="txt block" id="number" name="number" value="<?= $team['number']; ?>">
            </td>
        </tr>
      </table>
      <div id="grade_check_res" style="display: none;"></div>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="275px">
                Полное имя учителя: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <table width="100%" id="teachers">
                <?php
                    if (count($teachers)>0){
                        foreach ($teachers as $teacher) {
                            echo("<tr><td><input ".($is_user_admin?'':'readonly="true"')." type='text' class='txt block' name='teachers[]' onblur='check_frm_teacher ();' value='".htmlspecialchars(stripslashes($teacher['FIO']))."'><input type='hidden' name='teacher_team[]' value='".$teacher['idOfTeacher_team']."'/></td><td width='24' style='text-align:right;'><img class='btn' src='".config_get('document-root')."/pics/cross.gif'/></td></tr>");
                        }
                    }
                    else {
                        echo("<tr><td><input ".($is_user_admin?'':'readonly="true"')." type='text' class='txt block' name='teachers[]'/><input type='hidden' name='teacher_team[]'/></td><td width='24' style='text-align:right;'><img class='btn' src='".config_get('document-root')."/pics/cross.gif'/></td></tr>");                        
                    }
                ?>
                </table>
                <button id="addTeacher" type="button" class="submitBtn">Добавить</button>
            </td>
        </tr>
      </table>
      <div id="teacher_check_res" style="display: none;"></div>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="275px">
                Полное имя 1-го участника: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input type="text" class="txt block" id="pupil1_full_name" name="pupils[]" onblur="check_frm_pupil ();" value="<?= htmlspecialchars(stripslashes($pupils[0]["FIO"])); ?>">
                <input type='hidden' name='pupil_team[]' value='<?=$pupils[0]['idOfPupil_team']?>'/>
            </td>
        </tr>
      </table>
      <div id="pupil_check_res" style="display: none;"></div>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="275px">
                Полное имя 2-го участника:
            </td>
            <td style="padding: 0 2px;">
                <input type="text" class="txt block" id="pupil2_full_name" name="pupils[]" value="<?= htmlspecialchars(stripslashes($pupils[1]["FIO"])); ?>">
                <input type='hidden' name='pupil_team[]' value='<?=$pupils[1]['idOfPupil_team']?>'/>
            </td>
        </tr>
      </table>
      <div id="hr"></div>
      <table class="clear" width="100%">
        <tr><td width="275px">
                Полное имя 3-го участника:
            </td>
            <td style="padding: 0 2px;">
                <input type="text" class="txt block" id="pupil3_full_name" name="pupils[]" value="<?= htmlspecialchars(stripslashes($pupils[2]["FIO"])); ?>">
                <input type='hidden' name='pupil_team[]' value='<?=$pupils[2]['idOfPupil_team']?>'/>
            </td>
        </tr>
      </table>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="275px">
                В какой день участвует:
            </td>
            <td style="padding: 0 2px;">
                <select id="contest_day" name="contest_day" <?=$is_user_admin?'':'readonly="true"'?>>
                    <option value="сб" <?= $team['contest_day']=='сб'?'selected="selected"':'' ?>>суббота</option>
                    <option value="вс" <?= $team['contest_day']=='вс'?'selected="selected"':'' ?>>воскресенье</option>
                </select>
            </td>
        </tr>
      </table>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="275px">
                В какую смену учится:
            </td>
            <td style="padding: 0 2px;">
                <select id="smena" name="smena" <?=$is_user_admin?'':'readonly="true"'?> >
                    <option value="1" <?= $team['smena']=='1'?'selected="selected"':'' ?>>1 смена (играет с 14 до 16 часов местного времени)</option>
                    <option value="2" <?= $team['smena']=='2'?'selected="selected"':'' ?>>2 смена (играет с 11 до 13 часов местного времени)</option>
                </select>
            </td>
        </tr>
      </table>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="275px">
                Примечание:
            </td>
            <td style="padding: 0 2px;">
                <input <?=$is_user_admin?'':'readonly="true"'?> type="text" id="comment" name="comment" onblur="check_frm_comment ();" value="<?= htmlspecialchars(stripslashes($team['comment'])); ?>" class="txt block">
            </td>
        </tr>
      </table>
      <div id="comment_check_res" style="display: none;"></div>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="275px">
                Платеж подтвержден:
            </td>
            <td style="padding: 0 2px;">
                <input type="checkbox" value="1" <?=($team['is_payment'])?'CHECKED':''?> id="is_payment" name="is_payment" onchange="document.getElementById('is_payment_value').value = document.getElementById('is_payment').checked ? 1 :0;"></input>
                <?php
                    if ($team['payment_id']!='' && $team['payment_id']!='-1')
                    {
                        echo('<a href="../../payment/all/?action=edit&id='.$team['payment_id'].'">Просмотр</a>');
                    }
                    else
                    {
                        echo('<label>Просмотр</label>');
                    }
                ?>
                
                <input type="hidden" value="<?=($team['is_payment'])?'1':'0'?>" id="is_payment_value" name="is_payment_value"></input>
            </td>
        </tr>
      </table>

  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?<?= (($page != '') ? ('&page=' . $page) : ('')); ?>');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
    formc ();
?>
