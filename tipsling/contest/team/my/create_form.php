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

global $page, $current_contest;

dd_formo('title=Новая команда;');
?>
<script language="JavaScript" type="text/javascript">
  function check(frm) {   
    var grade  = getElementById ('grade').value;
    var pupil1_full_name   = getElementById ('pupil1_full_name').value;
    var $teachers = $('input[name="teachers[]"]');
    var comment = qtrim(getElementById('comment').value);
    
    if (qtrim(grade)==''){
      alert('Укажите класс команды');
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

    if (comment.length > <?=opt_get('max_comment_len');?>) {
      alert("Поле \"Комментарий\" не может содержать более <?=opt_get('max_comment_len');?> символов");
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
            $teachers.find('tr:last').after("<tr><td><input type='text' class='txt block' name='teachers[]' onblur='check_frm_teacher ();' value=''/></td><td width='24' style='text-align:right;'><img class='btn' src='<?=config_get('document-root')?>/pics/cross.gif'/></td></tr>");
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
<div>
  <form action=".?action=create&page=<?= $page ?>" method="POST" onsubmit="check (this); return false;">
    <table class="clear" width="100%">
        <tr>
            <td width="30%" style="padding: 0 2px;">
                Класс участников: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input type="text" class="txt block" id="grade" name="grade" onblur="check_frm_grade ();" value="<?= htmlspecialchars(stripslashes($_POST['grade'])); ?>">
            </td>
        </tr>
        <tr><td><i>(Для ВУЗов: 1 курс = 12 класс, 2 курс = 13 и т.д).</i></td></tr>
    </table>
    <div id="grade_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class ="clear" width="100%">
        <tr><td width="30%">
                Полное имя учителя: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <table width="100%" id="teachers">
                <?php
                    $u = user_get_by_id(user_id());
                    $teacher_full_name = $u['surname'] . ' ' . $u['name'] . (($u['patronymic'] == '') ? ('') : (' ' . $u['patronymic']));
                    print("<tr><td><input type='text' class='txt block' name='teachers[]' value='" . $teacher_full_name . "'/><input type='hidden' name='teacher_team[]'/></td><td width='24' style='text-align:right;'><img class='btn' src='".config_get('document-root')."/pics/cross.gif'/></td></tr>");
                ?>
                </table>
                <button id="addTeacher" type="button" class="submitBtn">Добавить</button>
            </td>
        </tr>
    </table>
    <div id="teacher_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class ="clear" width="100%">
        <tr><td width="30%">
                Полное имя 1-го участника: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input type="text" class="txt block" id="pupil1_full_name" name="pupils[]" onblur="check_frm_pupil ();" value="">
            </td>
        </tr>
    </table>
    <div id="pupil_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class ="clear" width="100%">
        <tr><td width="30%">
                Полное имя 2-го участника:
            </td>
            <td style="padding: 0 2px;">
                <input type="text" class="txt block" id="pupil2_full_name" name="pupils[]" value="">
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class ="clear" width="100%">     
        <tr><td width="30%">
                Полное имя 3-го участника:
            </td>
            <td style="padding: 0 2px;">
                <input type="text" class="txt block" id="pupil3_full_name" name="pupils[]" value="">
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class ="clear" width="100%">
        <tr><td width="30%">
                В какой день участвует:
            </td>
            <td style="padding: 0 2px;">
                <select id="contest_day" name="contest_day">
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
                <select id="smena" name="smena">
                    <option value="1">1 смена (играет с 14 до 16 часов местного времени)</option>
                    <option value="2">2 смена (играет с 11 до 13 часов местного времени)</option>
                </select>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Дата оплаты оргвзноса:
            </td>
            <td style="padding: 0 2px;">
                <?= calendar('date') ?>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Адреса репостов:
            </td>
            <td style="padding: 0 2px;">
                <table width="100%" id="reposts">
                    <tr>
                        <td>
                            <input type='text' class='txt block' name='repost[]'/>
                        </td>
                        <td width='24' style='text-align:right;'>
                            <img class='btn' src='<?=config_get('document-root')?>/pics/cross.gif'/>
                        </td>
                    </tr>
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
                <input type="text" id="comment" name="comment" onblur="check_frm_comment ();" value="<?= htmlspecialchars(stripslashes($_POST['comment'])); ?>" class="txt block">
            </td>
        </tr>
    </table>
    <div id="comment_check_res" style="display: none;"></div>
    <div class="formPast">
      <button class="submitBtn block" type="submit">Сохранить</button>
    </div>
  </form>
</div>

<script>
    $(function(){
        var AddRepostField = function(){
            $('#reposts').find('tr:last').after("<tr><td><input type='text' class='txt block' name='repost[]' value=''/></td><td width='24' style='text-align:right;'><img class='btn' src='<?=config_get('document-root')?>/pics/cross.gif'/></td></tr>");
        },
        RemoveRepostField = function(){
            var $rows = $(this).closest('table').find('tr');
            if ($rows.length>1){
                $(this).closest('tr').remove();
            }
        };
        
        $('#addRepost').on('click', AddRepostField);
        $('#reposts').on('click', 'img',  RemoveRepostField);        
    });
</script>


<?php
      dd_formc ();
      
      $this_contest = contest_get_by_id($current_contest);
      
      $sql = "SELECT * FROM contest where ".
             "family_id=".$this_contest['family_id']." and ".
             "DATE_FORMAT(contest_finish,'%Y-%m-%d')<=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d') and ".
             "DATE_FORMAT(send_to_archive,'%Y-%m-%d')>DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d')";
      $contest_list = arr_from_query($sql);
      
      if (count($contest_list)>0)
      {
        $whereContests = '';
        for ($i=0; $i<count($contest_list); $i++)
        {
            if ($i+1<count($contest_list))
                $whereContests .= 'contest_id = '.$contest_list[$i]['id'].' or ';
            else
                $whereContests .= 'contest_id = '.$contest_list[$i]['id'];
        }
        
        $sql = "SELECT team.id, team.grade, team.number, team.contest_id, "
              ." (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=1) as pupil1, "
              ." (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=2) as pupil2, "
              ." (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=3) as pupil3 "
              ."FROM team "
              ."WHERE responsible_id=".user_id(). " AND (".$whereContests.")";
        $team_list = arr_from_query($sql);
        
        if (count($contest_list)>0 && count($team_list)>0)
        {
            dd_formo('title=Зарегистрировать команду из предыдущих конкурсов;');
            echo('
<div>
  <form action=".?action=register_again" method="POST">
      <table class="clear" width="100%">
            <td width="15%" style="padding: 0 2px;">
                Команда:
            </td>
            <td style="padding: 0 2px;">
                <select id="Team" name ="Team">');
             foreach ($team_list as $t)
             {
                $t_contest = contest_get_by_id($t['contest_id']);
                echo('<option value = "' . $t['id'] . '" >' . 
                     $t['grade'].'.'.$t['number'].' ('.
                     $t_contest['name'].
                     ') - '.$t['pupil1'].
                     ($t['pupil2']!=null
                     ?', '.$t['pupil2']:'').
                     ($t['pupil3']!=null
                     ?', '.$t['pupil3']:'').
                     '</option>');
             }
             echo('
                 </select>
            </td>
      </table>
    <div class="formPast">
      <button class="submitBtn block" type="submit">Сохранить</button>
    </div>
  </form>
</div>');
        }
      }
    if (count($contest_list)>0 && count($team_list)>0)
      dd_formc();
?>