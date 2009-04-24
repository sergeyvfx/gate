<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Datatype edit form generation script
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

  global $id, $viewelement;

  $d = new CDataSet ();
  $d->Init ($id);
  $refCount = manage_dataset_refcount ($id);

  if ($viewelement != '') {
    include 'field.php';
  } else {
    formo ('title=Информация о наборе данных;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var className = getElementById ('className').value;
    if (qtrim (className) == '') {
      alert ('Нельзя сменить имя набора данных на пустое.');
      return false;
    }
    frm.submit ();
  }
</script>

<form action=".?action=save&id=<?=$id;?>" method="post" onsubmit="check (this); return false;" name="frm">
  Название набора данных:
  <input type="text" id="className" name="className" value="<?=$d->GetName ();?>" class="txt block"><div id="hr"></div>
</form>

<?php
    global $act, $eid, $down;
    if ($act == 'append')      manage_dataset_append_received_field ($id);
    if ($act == 'saveelement') manage_dataset_save_field ($id, $eid);
    if ($act == 'delete')      manage_dataset_delete_field ($id, $eid);
    if ($act == 'togimp')      manage_dataset_toggle_elem_importancy ($id, $eid);
    if ($act == 'togvis')      manage_dataset_toggle_elem_invisibility ($id, $eid);
    if ($act == 'down')        manage_dataset_down_field ($id, $eid);
    if ($act == 'up')          manage_dataset_up_field ($id, $eid);

    $list = manage_dataset_get_fields ($id);

    if (count ($list) > 0) {
      include 'fields.php';
    }

    if ($refCount == 0) {
      include 'append_form.php';
    }
?>

  <div class="formPast"<?=(($refCount!=0)?(' style="padding-top: 0;"'):(''));?>>
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="button" onclick="check (frm);">Сохранить</button>
  </div>

<?php
    formc ();
  }
?>
