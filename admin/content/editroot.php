<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for root element editor form generation
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF!='') {
    print ('HACKERS?');
    die;
  }

  $id = 1;
  formo ('title=Редактирование корневого раздела;');
  $c = wiki_spawn_content ($id);
  $si = $c->GetSecurityInformation ();
  $glist = security_groups ();
?>
<script language="JavaScript" type="text/javascript">
  var gDesc = new Array ();
<?php
  foreach ($glist as $k=>$g) {
?>
  gDesc[<?=$g['access']?>]='<?=addslashes ($g['desc'])?>';
<?php
  }
?>

  function updateGDesc (sender) {
    getElementById ('gdesc').innerHTML = gDesc[sender.value];
  }

  function check (frm) {
    sef_prepare_post_data ('security');
    frm.submit ();
  }
</script>

<form action=".?action=save&id=1" method="POST" onsubmit="check (this); return false;">
  <h3>Права доступа:</h3><div id="hr"></div>
<?php
  $si->EditForm ();
?>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>

<?php
  formc ();
?>
