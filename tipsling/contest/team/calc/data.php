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

if (!user_authorized ()) {
  header('Location: ../../../login');
}

if (!is_responsible(user_id())) {
  print (content_error_page(403));
  return;
}

if (!is_responsible_has_school(user_id())) {
  redirect(config_get('document-root') . '/login/profile/info/school/?noschool=1');
}

if ($current_contest=='' || $current_contest==-1)
    header('Location: ../../choose');


$contest = contest_get_by_id($current_contest);
//$contest_stat = get_contest_status($current_contest);
$allow_registration = check_create_team_allow($current_contest);
$allow_edit = check_edit_team_allow($current_contest);
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/calc" ?>">Команды</a>Калькулятор скидок</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include '../menu.php';
    
    $team_menu->SetActive('calc');
    $team_menu->Draw();    
    ?>
      <br/>
      Выберите применяемые скидки:</br>
      <input type="checkbox" id="repost" value="100">[100р]Скидка за распространение информации о конкурсе (не менее 10 сообщений о конкурсе в сети)</br>
      <input type="checkbox" id="early" value="100">[100р]Скидка за раннюю оплату (до 1 марта)</br>
      <input type="checkbox" id="years" value="100">[100р]Скидка за возраст (для команд с 1 по 9 класс)</br>
      <input type="checkbox" id="participant" value="100">[100р]Скидка участникам предыдущих конкурсов (хотя бы один из учеников уже принимал участие в конкурсе)</br>
      <input type="checkbox" id="veteran" value="100">[100р]Скидка "ветеранам" конкурса (хотя бы один из учеников принимал участие в конкурсе 3 или более раз)</br>
      <input type="checkbox" id="winer" value="100">[100р]Скидка призерам предыдущих конкурсов (хотя бы один из учеников занимал призовое место в одном из предыдущих конкурсов)</br>
      <input type="checkbox" id="teacher_participant" value="100">[100р]Скидка учителям-участникам прежних конкурсов</br>
      <input type="checkbox" id="teacher_winer" value="100">[100р]Скидка учителям-победителям прежних конкурсов</br>
      <input type="checkbox" id="other_contest" value="100">[100р]Скидка за участие в конкурсах Российской ассоциации ТРИЗ и ТРИЗ-Саммита</br>
      </br>
      </br>
      Суммарная скидка: <input type="text" id="discount" readonly="readonly" value="0"/>
      Оргвзнос: <input type="text" id="result" readonly="readonly" value="1300"/>
  </div>
</div>

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
</script>
