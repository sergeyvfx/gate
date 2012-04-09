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
formo('title=Редактирование команды;');

$team = team_get_by_id($id);
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var grade  = getElementById ('grade').value;
    var teacher_full_name = getElementById('teacher_full_name').value;
    var pupil1_full_name   = getElementById ('pupil1_full_name').value;

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

    if (qtrim(teacher_full_name)==''){
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
      var teacher = getElementById ('teacher_full_name').value;

      if (qtrim(teacher)==''){
          show_msg ('teacher_check_res', 'err', 'Это поле обзательно для заполнения');
          return;
      }

      hide_msg('teacher_check_res');
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
</script>

<form action=".?action=save&id=<?= $id; ?>&<?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST" onsubmit="check (this); return false;">
      <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Класс участников: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input <?=$is_user_admin?'':'readonly="true"'?> type="text" class="txt block" id="grade" name="grade" onblur="check_frm_grade ();" value="<?= htmlspecialchars(stripslashes($team['grade'])); ?>">
            </td>
        </tr>
        <tr><td><i>(Для ВУЗов: 1 курс = 12 класс, 2 курс = 13 и т.д.)</i></td></tr>
      </table>
    <div id="grade_check_res" style="display: none;"></div>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="30%">
                Полное имя учителя: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input <?=$is_user_admin?'':'readonly="true"'?> type="text" class="txt block"  id="teacher_full_name" name="teacher_full_name" onblur="check_frm_teacher ();" value="<?= htmlspecialchars(stripslashes($team['teacher_full_name']));?>">
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
                <input <?=$is_user_admin?'':'readonly="true"'?> type="text" class="txt block" id="pupil1_full_name" name="pupil1_full_name" onblur="check_frm_pupil ();" value="<?= htmlspecialchars(stripslashes($team['pupil1_full_name'])); ?>">
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
                <input <?=$is_user_admin?'':'readonly="true"'?> type="text" class="txt block" id="pupil2_full_name" name="pupil2_full_name" value="<?= htmlspecialchars(stripslashes($team['pupil2_full_name'])); ?>">
            </td>
        </tr>
      </table>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="30%">
                Полное имя 3-го участника:
            </td>
            <td style="padding: 0 2px;">
                <input <?=$is_user_admin?'':'readonly="true"'?> type="text" class="txt block" id="pupil3_full_name" name="pupil3_full_name" value="<?= htmlspecialchars(stripslashes($team['pupil3_full_name'])); ?>">
            </td>
        </tr>
      </table>
      <div id="hr"></div>
      <table class ="clear" width="100%">
        <tr><td width="30%">
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
        <tr><td width="30%">
                Платеж подтвержден:
            </td>
            <td style="padding: 0 2px;">
                <input type="checkbox" value="1" <?=($team['is_payment'])?'CHECKED':''?> id="is_payment" name="is_payment"></input>
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
