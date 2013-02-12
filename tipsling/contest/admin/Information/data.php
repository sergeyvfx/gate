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

global $current_contest, $document_root;

if (!user_authorized ()) {
  header('Location: ../../../../login');
}

$it = contest_get_by_id($current_contest);
$query = arr_from_query("select * from Admin_FamilyContest ".
                   "where family_contest_id=".$it['family_id']." and ".
                   "user_id=".user_id());
if (count ($query) <= 0)
{
  print (content_error_page(403));
  return;
}
?>

<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a><a>Администрирование</a>Статистика</div>
${information}
<div class="form">
  <div class="content">
    <?php
    include '../menu.php';
    $admin_menu->SetActive('Information');
    
    $admin_menu->Draw();
    include 'list.php';
    ?>


<!--
Attemp to write not standart query to web site

<script src="../../../../scripts/jquery-1.7.1.js"></script>
<script src="../../../../scripts/jquery.semanticTabs.js"></script>
<script language="JavaScript" type="text/javascript">
$(function() {
	$('.b-tabs').semanticTabs({
		tabSelector: '> h2',
		bodySelector: '> div'
	});
});

function OnBtnUpClick()
{
    var chosenColumnList = getElementById('ChosenColumns');
    if (chosenColumnList.options.selectedIndex>0)
    {
        var oOption = chosenColumnList.options[chosenColumnList.options.selectedIndex];
        var oPrevOption = chosenColumnList.options[chosenColumnList.options.selectedIndex-1];
        chosenColumnList.insertBefore(oOption, oPrevOption);
    }
}

function OnBtnDownClick()
{
    var chosenColumnList = getElementById('ChosenColumns');
    if (chosenColumnList.options.selectedIndex!=-1 && 
            chosenColumnList.options.selectedIndex<chosenColumnList.options.length-1)
    {
        var oOption = chosenColumnList.options[chosenColumnList.options.selectedIndex];
        var oNextOption = chosenColumnList.options[chosenColumnList.options.selectedIndex+1];
        chosenColumnList.insertBefore(oNextOption, oOption);
    }
}

function OnBtnLeftClick()
{
    var chosenColumnList = getElementById('ChosenColumns');
    var AllColumnList = getElementById('AllColumns');
    for (var i=0; i < AllColumnList.options.length; i++)
        if (AllColumnList.options[i].selected) 
            chosenColumnList.options[chosenColumnList.options.length] = AllColumnList.options[i];
}

function OnBtnRightClick()
{
    var chosenColumnList = getElementById('ChosenColumns');
    if (chosenColumnList.options.selectedIndex!=-1)
        chosenColumnList.options.remove(chosenColumnList.options.selectedIndex);
}
</script>



<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a><a>Администрирование</a>Нестандартный запрос</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $action, $id;
    include '../menu.php';
    $admin_menu->SetActive('Information');
    
    $admin_menu->Draw();

    ?>
      
      <div class="b-tabs">
	<h2>Поля</h2>
	<div>
            <table width="100%">
                <tr>
                    <td width="50%">
                        <select size="20" id="ChosenColumns" style="width: 100%; height: 100%" >
                            <option value="responsible.surname">Фамилия ответственного</option>
                            <option value="responsible.name">Имя ответственного</option>
                            <option value="responsible.patronic">Отчетсво ответственного</option>
                            <option value="responsible.email">Email ответственного</option>
                            <option value="responsible.phone">телефон ответственного</option>
                            <option value="school.Name">Учебное заведение</option>
                            <option value="school_status.name">Статус учебного заведения</option>
                        </select>
                    </td>
                    <td width="30">
                        <div style="vertical-align: middle; text-align: center;">
                            <button onclick="OnBtnUpClick()" style="margin: 5px; width: 22px; height: 22px; background-position:center; background-repeat:no-repeat;  background-image:url('../../../../pics/up.png');"/>
                            <button onclick="OnBtnDownClick()" style="margin: 5px; width: 22px; height: 22px; background-position:center; background-repeat:no-repeat; background-image:url('../../../../pics/down.png');"/>
                            <button onclick="OnBtnLeftClick()" style="margin: 5px; width: 22px; height: 22px; background-position:center; background-repeat:no-repeat; background-image:url('../../../../pics/left.png');"/>
                            <button onclick="OnBtnRightClick()" style="margin: 5px; width: 22px; height: 22px; background-position:center; background-repeat:no-repeat; background-image:url('../../../../pics/right.png');"/>
                        </div>
                    </td>
                    <td width="50%">
                        <select multiple size="20" id="AllColumns" style="width: 100%; height: 100%">
                            <option value="responsible.surname">Фамилия ответственного</option>
                            <option value="responsible.name">Имя ответственного</option>
                            <option value="responsible.patronic">Отчетсво ответственного</option>
                            <option value="responsible.email">Email ответственного</option>
                            <option value="responsible.phone">телефон ответственного</option>
                            <option value="school.Name">Учебное заведение</option>
                            <option value="school_status.name">Статус учебного заведения</option>
                            <option value="country.name+' '+city.name+' '+school.street+school.house">Адрес учебного заведения</option>
                            <option value="timezone.offset">Часовой пояс</option>
                            <option value="team.grade+'.'+team.number">Номер команды</option>
                            <option value="team.teacher_full_name">ФИО учителя команды</option>
                            <option value="team.pupil1_full_name">ФИО первого участника команды</option>
                            <option value="team.pupil2_full_name">ФИО второго участника команды</option>
                            <option value="team.pupil3_full_name">ФИО третьего участника команды</option>
                            <option value="contest.name">Конкурс</option>                            
                        </select>
                    </td>
                </tr>
            </table>
	</div>
	<h2>Ограничение</h2>
	<div>
		<p>Форма для задания ограничения на список</p>
	</div>
	<h2>
            Результат
        </h2>
	<div>
		<p>Тут будет результат</p>
	</div>
      </div>
  </div>
</div>
-->