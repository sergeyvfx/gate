<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Section drawing script
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

  global $section;
  $items = manage_settings_get_section_elements ($section);
  $update_forms = array ();
  $check_scripts = array ();

  for ($i = 0; $i < count ($items); $i++) {
    $it = $items[$i];
    $update_forms[]  = manage_settings_get_config_form ($it['class'], $it['ident'], $it['settings']);
    $check_scripts[] = manage_settings_get_check_script ($it['class'], $it['ident'], $it['settings']);
  }

  formo ('title=Список настроек в секции;');
?>
<script language="JavaScript" type="text/javascript">
  function update_check (frm) {
<?php
  for ($i = 0; $i < count ($items); $i++)
    if ($check_scripts[$i] != '') {
      print $check_scripts[$i]."\n";
    }
?>
    frm.submit ();
  }
</script>

<form action=".?section=<?=$section;?>&action=save" method="post" onsubmit="update_check (this); return false;">
<?php
  for ($i = 0; $i < count ($items); $i++) {
    $it = $items[$i];
    $d = !$it['used'];
?>
<table width="100%" class="">
  <tr class="last"><td><a href=".?section=<?=$section?>&action=edit&id=<?=$it['id']?>"><?=$it['name'];?></a>&nbsp;-&nbsp;<i><?=$it['ident'];?></i></td><td width="48" align="right"><?ibtnav ('edit.gif', '?section='.$section.'&action=edit&id='.$it['id'], 'Редактировать элемент');?><?ibtnav (($d)?('cross.gif'):('cross_d.gif'), ($d)?('?section='.$section.'&action=delete&id='.$it['id']):(''), 'Удалить элемент', 'Удалить этот элемент?');?></td></tr>
  <tr class="last">
    <td colspan="3">
<?=$update_forms[$i];?><?php if ($i!=count ($items)-1) print '<div id="hr"></div>'; ?>
    </td>
  </tr>
</table>
<?php
    if ($i != count ($items)-1) {
      print ('<br>');
    }
  }
?>
  <div class="formPast" style="margin: 4px;">
    <button class="submitBtn block" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
