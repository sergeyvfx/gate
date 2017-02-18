<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Service edtit form generator
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
  $item = teamType_get_by_id($id);
  formo ('title=Информация о типе команды;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Нельзя сменить название типа команды на пустое.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=save&id=<?=$id;?>" method="post" onsubmit="check (this); return false;">
  <input type="hidden" id="root" name="root" value="<?= config_get ('document-root'); ?>"/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Название типа команды
      </td>
      <td style="padding: 0 2px;">
        <input type="text" id="name" name="name" value="<?=$item['name']?>" class="txt block"></div>
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
        <input type="text" class="txt block" name="description" id="description" value="<?=$item['description']?>">
      </td>
    </tr>
  </table>
  <hr/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Название класса (класс, курс и т.д.):
      </td>
      <td style="padding: 0 2px;">
        <input type="text" class="txt block" name="grade_name" id="grade_name" value="<?=$item['grade_name']?>">
      </td>
    </tr>
  </table>
  <hr/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Минимальный номер класса/курса:
      </td>
      <td style="padding: 0 2px;">
        <input type="text" class="txt block" name="grade_start_number" id="grade_start_number" value="<?=$item['grade_start_number']?>">
      </td>
    </tr>
  </table>
  <hr/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Максимальный номер класса/курса:
      </td>
      <td style="padding: 0 2px;">
        <input type="text" class="txt block" name="grade_max_number" id="grade_max_number" value="<?=$item['grade_max_number']?>">
      </td>
    </tr>
  </table>
  <hr/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Смещение номера команды:
      </td>
      <td style="padding: 0 2px;">
        <input type="text" class="txt block" name="grade_offset_number" id="grade_offset_number" value="<?=$item['grade_offset_number']?>">
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
          <input type="checkbox" name="is_out_of_contest" id="name" value="1" <?= ($item['is_out_of_contest'] ? 'checked="checked"' : '')  ?>>
      </td>
    </tr>
  </table>
  
  <hr/>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
