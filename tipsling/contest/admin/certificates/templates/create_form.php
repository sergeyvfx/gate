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

global $page;

dd_formo('title=Новый сертификат');
?>
<script language="JavaScript" type="text/javascript">
  function check(frm) {
    var certificate_name = qtrim(getElementById ('name').value);
    
    if (certificate_name == '') {
      alert("Поле \"Название\" обязательно для заполнения");
      return;
    }
    
    frm.submit ();
  }

  function check_frm_name() {
    var name = getElementById ('name').value;

    if (qtrim(name)=='') {
        show_msg ('name_check_res', 'err', 'Это поле обязательно для заполнения');
        return;
    }

    hide_msg('name_check_res');
  }
</script>
<div>
  <form action=".?action=create&page=<?=$page?>" method="POST" onsubmit="check(this); return false;">
    <table class="clear" width="100%">
        <tr><td width="70px" style="padding: 0 2px;">
                Название: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input type="text" id="name" name="name" onblur="check_frm_name ();" value="<?= htmlspecialchars(stripslashes($_POST['name'])); ?>" class="txt block"/>
            </td>
        </tr>
    </table>
    <div id="name_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="70px" style="padding: 0 2px;">
                Шаблон: 
            </td>
            <td style="padding: 0 2px;">
                <textarea width="100%" rows="15" id="template" name="template" class="txt block"><?= htmlspecialchars(stripslashes($_POST['template'])); ?></textarea>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    
    <div class="formPast">
      <button class="submitBtn block" type="submit">Сохранить</button>
    </div>
  </form>
</div>
<?php
dd_formc ();
?>
