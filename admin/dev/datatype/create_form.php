<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Datatype creation form generation script
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

  dd_formo ('title=Создать новый тип данных;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var className = getElementById ('className').value;
    var baseClass = getElementById ('dcName').value;

    if (qtrim (className) == '') {
      alert ('Имя создаваемого класса не может быть пустым.');
      return false;
    }

    var res = eval ('check_'+baseClass+' ();');

    if (!res) {
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=create" method="post" onsubmit="check (this); return false;">
  Название нового типа данных:
  <input type="text" id="className" name="className" value="<?=htmlspecialchars (stripslashes($_POST['className']));?>" class="txt block">
  <div id="hr"></div>
  Базовый тип:
  <select class="block" onchange="man_dtypes_class_changed (this);" id="dcName" name="dcName">
<?php
  $classes = content_Registered_DCClasses ();
  for ($i = 0; $i < count ($classes); $i++) {
    $class = $classes[$i];
    print ('<option value="'.$class['class'].'"'.(($_POST['dcName']==$class['class'])?(' selected'):('')).'>'.$class['class'].(($class['pseudonym']!='')?(' - '.$class['pseudonym']):('')).'</option>');
  }
?>
  </select>
  <div id="hr"></div><br>
  <b>Настройкм типа данных:</b><div id="hr"></div>
  <div id="man_dtypes_forms">
<?php
  for ($i = 0; $i < count ($classes); $i++) {
    $className = $classes[$i]['class'];
    $t = new $className ();
    $t->Init ();
    $invis = true;
    if (($_POST['dcName'] == '' && $i == 0) ||
        ($_POST['dcName'] != '' && $_POST['dcName'] == $className)) {
      $invis = false;
    }
?>
  <div id="dts_<?=$className?>"<?=(($invis)?('style="display: none;"'):(''))?>>
    <script language="JavaScript" type="text/javascript">function check_<?=$className?> () {<?=$t->CheckConfigScript ();?> return true;}</script>
<?php
  $t->SettingsForm ();
?>
  </div>
<?php
  }
?>
  </div>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>
<?php
  dd_formc ();
?>
