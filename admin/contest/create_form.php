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

  dd_formo ('title=Создать новый конкурс;');
  $list = contest_list();
?>
<script language="JavaScript" type="text/javascript">
  //var cur_service='<?=$list[0]['class']?>';
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Название создаваемого конкурса не может быть пустым.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=create" method="POST" onsubmit="check (this); return false;">
  <table class="clear" width="100%">
    <tr>
      <td width="15%" style="padding: 0 2px;">
        Название нового конкурса
      </td>
      <td style="padding: 0 2px;">
        <input type="text" class="txt block" name="name" id="name"> <!-- value="<?=$_POST['name'];?>">-->
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
                            $selected = ($k['id'] == $_POST['family_id']) ? ('selected') : ('');
                            echo('<option value = "' . $k['id'] . '" '.$selected.' >' . $k['name'] . '</option>');
                        }
                    ?>
        </select>
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
