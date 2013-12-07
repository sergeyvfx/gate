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
  function check(frm) {
    var contest_name = qtrim(getElementById ('name').value);
    var r_s = qtrim(getElementById('registration_start').value);
    var r_f = qtrim(getElementById('registration_finish').value);
    var c_s = qtrim(getElementById('contest_start').value);
    var c_f = qtrim(getElementById('contest_finish').value);
    var s_to_a = qtrim(getElementById('send_to_archive').value);
    
    if (contest_name == '') {
      alert("Поле \"Название\" обязательно для заполнения");
      return;
    }
    
    if (r_s!=null && r_f!=null && r_s>r_f) {
      alert("Конец регистрации не может быть раньше, чем ее начало");
      return;
    }
    
    if (c_s!=null && c_f!=null && c_s>c_f) {
      alert("Конец конкурса не может быть раньше, чем его начало");
      return;
    }
    
    if (s_to_a!=null && c_f!=null && s_to_a<c_f) {
      alert("Незавершенный конкурс не может быть добавлен в архивные");
      return;
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
  <div id="hr"></div>
  <table class="clear" width="100%">
      <tr><td width="15%" style="padding: 0 2px;">
              Начало регистрации:
          </td>
          <td style="padding: 0 2px;">
              <?= calendar('registration_start', htmlspecialchars(stripslashes($_POST['registration_start']))) ?>
          </td>
      </tr>
  </table>
  <div id="r_s_check_res" style="display: none;"></div>
  <div id="hr"></div>
  <table class="clear" width="100%">
      <tr><td width="15%" style="padding: 0 2px;">
              Конец регистрации:
          </td>
          <td style="padding: 0 2px;">
              <?= calendar('registration_finish', htmlspecialchars(stripslashes($_POST['registration_finish']))) ?>
          </td>
      </tr>
  </table>
  <div id="r_f_check_res" style="display: none;"></div>
  <div id="hr"></div>
  <table class="clear" width="100%">
      <tr><td width="15%" style="padding: 0 2px;">
              Начало конкурса:
          </td>
          <td style="padding: 0 2px;">
              <?= calendar('contest_start', htmlspecialchars(stripslashes($_POST['contest_start']))) ?>
          </td>
      </tr>
  </table>
  <div id="c_s_check_res" style="display: none;"></div>
  <div id="hr"></div>
  <table class="clear" width="100%">
      <tr><td width="15%" style="padding: 0 2px;">
              Конец конкурса:
          </td>
          <td style="padding: 0 2px;">
              <?= calendar('contest_finish', htmlspecialchars(stripslashes($_POST['contest_finish']))) ?>
          </td>
      </tr>
  </table>
  <div id="r_s_check_res" style="display: none;"></div>
  <div id="hr"></div>
  <table class="clear" width="100%">
      <tr><td width="15%" style="padding: 0 2px;">
              Дата добавления в архив:
          </td>
          <td style="padding: 0 2px;">
              <?= calendar('send_to_archive', htmlspecialchars(stripslashes($_POST['send_to_archive']))) ?>
          </td>
      </tr>
  </table>
  <div id="s_to_a_check_res" style="display: none;"></div>
  <div id="hr"></div>
  
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>
<?php
  dd_formc ();
?>
