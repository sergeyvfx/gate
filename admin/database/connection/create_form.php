<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Setting creation form
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

  dd_formo ('title=Добавить связь;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var connection = getElementById ('connection').value;
    var connection_table = getElementById ('connection_table').value;

    if (qtrim (connection) == '' && qtrim (connection_table) == '') {
      alert ('Не задана ни промежуточная таблица, ни связь.');
      return false;
    }
    if (qtrim (connection) != '' && qtrim (connection_table) != '') {
      alert ('Нельзя указывать и промежуточную таблицу, и связь одновременно.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=create" method="POST" onsubmit="return check (this);">
  Таблица 1:
  <select id="table1" name="table1" class="txt block">
      <?php
        $query = "select * from `visible_table` where `visible`=1";
        $result = mysql_query($query);
        while($row = mysql_fetch_array($result))
        {
            echo ('<option value='.$row['id'].'>'.$row['table'].'</option>');
        }
      ?>
  </select>
  <div id="hr"></div>
  Таблица 2:
  <select id="table2" name="table2" class="txt block">
      <?php
        $query = "select * from `visible_table` where `visible`=1";
        $result = mysql_query($query);
        while($row = mysql_fetch_array($result))
        {
            echo ('<option value='.$row['id'].'>'.$row['table'].'</option>');
        }
      ?>
  </select>
  <div id="hr"></div>
  Промежуточная таблица:
  <select id="connection_table" name="connection_table" class="txt block">
      <option value=""></option>
      <?php
        $query = "select * from `visible_table` where `visible`=1";
        $result = mysql_query($query);
        while($row = mysql_fetch_array($result))
        {
            echo ('<option value='.$row['id'].'>'.$row['table'].'</option>');
        }
      ?>
  </select>
  <div id="hr"></div>
  Связь:
  <input type="text" id="connection" name="connection" class="txt block"/><div id="hr"></div>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Добавить</button>
  </div>
</form>
<?php
  dd_formc ();
?>
