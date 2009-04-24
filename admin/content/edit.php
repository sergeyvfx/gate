<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Sove main global definitions
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

  global $id;
  if ($id == 1) {
    include 'editroot.php';
  } else {
    print ('<div id="snavigator"><a href=".">Разделы</a>'.wiki_content_navigator ($id, 'action=edit')).'</div>';

    formo ('title=Редактирование раздела;');
    $c = wiki_spawn_content ($id,'security');
    $si = $c->GetSecurityInformation ();
    $glist = security_groups ();
?>

<script language="JavaScript" type="text/javascript">
  var gDesc=new Array ();
<?php
  foreach ($glist as $k=>$g) {
?>
  gDesc[<?=$g['access']?>]='<?=addslashes ($g['desc'])?>';
<?php
  }
?>
  function updateGDesc (sender) { getElementById ('gdesc').innerHTML=gDesc[sender.value]; }

  function check (frm) {
    var name = getElementById ('name').value;
    var path = getElementById ('path').value;

    if (qtrim (name) == '') {
      alert ('Имя создаваемого рздела не может быть пустым.');
      return false;
    }

    if (qtrim (path) == '') {
      alert ('Имя виртуальной папки не может быть пустым.');
      return false;
    }

    if (!check_folder (path)) {
      alert ('Название виртуальной папки является некоррекнтым.');
      return false;
    }

    sef_prepare_post_data ('security');
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

    if (!check_folder (path)) {
      show_msg ('path_check_res', 'err', 'Название виртуальной папки является некорректным.');
      return false;
    }

    ipc_send_request ('/', 'ipc=check_wiki_node&cpath='+path+'&pid=<?=$c->GetPID ();?>&skipId=<?=$id;?>', update_path_check);
  }
</script>

<form action=".?action=save&id=<?=$id;?>" method="POST" onsubmit="check (this); return false;">
  Название раздела:
  <input type="text" id="name" name="name" value="<?=$c->GetName ();?>" class="txt block"><div id="hr"></div>
  Название виртуальной папки:
  <input type="text" id="path" name="path" value="<?=$c->GetPath ();?>" class="txt block">
  <button class="block" type="button" onclick="check_path ();" style="margin-top: 4px;">Проверить</button>
  <div id="path_check_res" style="display: none;"></div><div id="hr"></div>
  <h3>Права доступа:</h3><div id="hr"></div>
<?$si->EditForm ();?>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>

<?php
    formc ();
  }
?>
