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
  $d = contest_get_by_id($id);
  formo ('title=Информация о конкурсе;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Нельзя сменить имя конкурса на пустое.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=save&id=<?=$id;?>" method="post" onsubmit="check (this); return false;">
  <table class="clear" width="100%">
    <tr>
      <td width="15%" style="padding: 0 2px;">
        Название конкурса
      </td>
      <td style="padding: 0 2px;">
        <input type="text" id="name" name="name" value="<?=$d['name']?>" class="txt block"><div id="hr"></div>
      </td>
    </tr>
  </table>
  <div id="hr"></div>
  <table class="clear" width="100%">
    <tr>
      <td width="15%" style="padding: 0 2px;">
        Семество конкурса
      </td>
      <td style="padding: 0 2px;">
        <select id="family_id" name ="family_id">
                    <?php
                        $sql = "SELECT\n"
                        . " * \n"
                        . "FROM\n"
                        . " family_contest \n";
                        $tmp = arr_from_query($sql);
                
                        foreach ($tmp as $k)
                        {
                            $selected = ($k['id'] == $d['family_id']) ? ('selected') : ('');
                            echo('<option value = "' . $k['id'] . '" '.$selected.' >' . $k['name'] . '</option>');
                        }
                    ?>
        </select>
      </td>        
    </tr>
  </table>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
