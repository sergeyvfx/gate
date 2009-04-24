<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Content creation form generation script
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

  dd_formo ('title=Создать новый раздел;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name   = getElementById ('name').value;
    var path   = getElementById ('path').value;
    var cclass = getElementById ('class').value;
    if (qtrim (name) == '') {
      alert ('Имя создаваемого рздела не может быть пустым.');
      return false;
    }

    if (qtrim (path) == '') {
      alert ('Имя виртуальной папки не может быть пустым.');
      return false;
    }

    if (!isalphanum (path)) {
      alert ('Название виртуальной папки может состоять только из букв латинского алфавита и цифр.');
      return false;
    }

    if (qtrim (cclass) == '') {
      alert ('Не указан используемый класс данных.');
      return false;
    }

    frm.submit ();
  }

  function update_path_check (http_request) {
    if (http_request.readyState == 4) {
      if (http_request.responseText == '+OK')
        show_msg ('path_check_res', 'ok', 'Данное название виртуальной папки является корректным и Вы можете его использовать.'); else
        show_msg ('path_check_res', 'err', 'Данное имя виртуальной папки уже используется в данной ветве дерева структуры сайта.');
    }
  }

  function check_path () {
    var path = getElementById ('path').value;

    if (qtrim (path) == '') {
      show_msg ('path_check_res', 'err', 'Имя виртуальной папки не может быть пустым.');
      return false;
    }

    if (!isalphanum (path)) {
      show_msg ('path_check_res', 'err', 'Название виртуальной папки может состоять только из букв латинского алфавита и цифр.');
      return false;
    }

    ipc_send_request ('/', 'ipc=check_wiki_node&cpath='+path+'&pid=1', update_path_check);
  }

  function update_settings_form () {
    var _class = getElementById ('class').value;
    hide ('CClass_settings_' + CClassName);
    sb ('CClass_settings_' + _class);

    CClassName = _class;
  }
</script>

<form action=".?action=create" method="POST" onsubmit="check (this); return false;">
  Название раздела:
  <input type="text" id="name" name="name" value="<?=htmlspecialchars (stripslashes($_POST['name']));?>" class="txt block"><div id="hr"></div>
  Название виртуальной папки:
  <input type="text" id="path" name="path" value="<?=htmlspecialchars (stripslashes($_POST['path']));?>" class="txt block">
  <button class="block" type="button" onclick="check_path ();" style="margin-top: 4px;">Проверить</button>
  <div id="path_check_res" style="display: none;"></div><div id="hr"></div>
  Класс:
<?php
  $cclasses=content_Registered_CClasses ();
?>
  <select name="class" id="class" class="block" onchange="update_settings_form ();">
<?php
  for ($i=0; $i<count ($cclasses); $i++) {
    $t=$cclasses[$i];
    $c=new $t['class'] ();
?>
    <option value="<?=$c->GetClassName ();?>"<?=(($c->GetClassName ()==$_POST['class'])?(' selected'):(''));?>><?=$t['pseudonym'];?></option>
<?php  } ?>
  </select><div id="hr"></div>
  <h3>Настройки</h3><div id="hr"></div>
<?php
  if ($_POST['class']=='')
    $vis=$cclasses[0]['class']; else
    $vis=$_POST['class'];
?>
    <script language="JavaScript" type="text/javascript">var CClassName="<?=$vis;?>";</script>
<?php for ($i=0; $i<count ($cclasses); $i++) {
    $t=$cclasses[$i];
    $c=new $t['class'] (); ?>
    <div id="CClass_settings_<?=$c->GetClassName ();?>"<?=(($c->GetClassName ()!=$vis)?(' class="invisible"'):(''));?>>
      <?=$c->DrawSettingsForm ('settings_form_'.$c->GetClassName ());?>
    </div>
<?php } ?>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>

<?php
  dd_formc ();
?>
