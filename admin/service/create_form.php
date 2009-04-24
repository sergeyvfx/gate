<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Service creation for generator
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

  dd_formo ('title=Создать новый сервис;');
  $list = content_Registered_SClasses ();
?>
<script language="JavaScript" type="text/javascript">
  var cur_service='<?=$list[0]['class']?>';
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Имя создаваемого сервиса не может быть пустым.');
      return false;
    }

    frm.submit ();
  }

  function update_settings_form () {
    var id = getElementById ('service').value;
    hide ('service_settings_' + cur_service);
    sb ('service_settings_' + id);
    cur_service = id;
  }
</script>

<form action=".?action=create" method="POST" onsubmit="check (this); return false;">
Название нового сервиса
  <input type="text" class="txt block" name="name" id="name" value="<?=$_POST['name'];?>"><div id="hr"></div>
Сервис:
  <select class="block" name="service" id="service" onchange="update_settings_form ();">
<?php
  for ($i = 0; $i < count ($list); $i++) {
    $it = $list[$i];
?>
    <option value="<?=$it['class']?>"<?=(($_POST['service']==$it['class'])?(' selected'):(''));?>><?=$it['pseudonym'];?></option>
<?php
  }
?>
  </select>
  <h3>Настройки</h3><div id="hr"></div>
<?php
  for ($i = 0; $i < count ($list); $i++) {
    $it = $list[$i];
    $c = manage_spawn_service (-1, $it['class']);
    $vis = false;
    if ($_POST['service'] == $it['class']) {
      $vis=true;
    } else if ($_POST['service'] == '' && $i == 0) {
      $vis=true;
    }
?>
  <div id="service_settings_<?=$it['class'];?>"<?=(($vis)?(''):(' style="display: none;"'));?>><?$c->DrawSettingsForm ();?></div>
<?php
  }
?>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>
<?php
  dd_formc ();
?>
