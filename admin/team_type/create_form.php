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

  dd_formo ('title=Создать новый тип команды;');
  $list = contest_list();
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Название не может быть пустым.');
      return false;
    }

    frm.submit ();
  }  
</script>

<form action=".?action=create" method="POST" onsubmit="check (this); return false;">
  <input type="hidden" id="root" name="root" value="<?= config_get ('document-root'); ?>"/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Название типа команды
      </td>
      <td style="padding: 0 2px;">
        <input type="text" class="txt block" name="name" id="name">
      </td>
    </tr>
  </table>
  <hr/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Описание
      </td>
      <td style="padding: 0 2px;">
        <input type="text" class="txt block" name="description" id="description">
      </td>
    </tr>
  </table>
  <hr/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Учавствуют вне конкурса
      </td>
      <td style="padding: 0 2px;">
        <input type="checkbox" name="is_out_of_contest" id="name" value="1">
      </td>
    </tr>
  </table>
  
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>
<?php
  dd_formc ();
?>
