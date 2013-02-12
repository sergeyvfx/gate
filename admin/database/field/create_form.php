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

  dd_formo ('title=Добавить поле;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var caption = getElementById ('caption').value;

    if (qtrim (caption) == '') {
      alert ('Заголовок поля не может быть пустым.');
      return false;
    }

    frm.submit ();
  }
  
  function table_changed()
  {
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        
      if (xmlhttp.readyState==4 && xmlhttp.status==200)
      {
          document.getElementById("field").innerHTML=xmlhttp.responseText;
      }
    }
    var value = document.getElementById("table").value;
    xmlhttp.open("GET","get_fields.php?table="+value,true);
    xmlhttp.send();
  }
</script>

<form action=".?action=create" method="POST" onsubmit="return check (this);">
  Таблица:
  <select id="table" name="table" class="txt block" onchange="table_changed();">
      <?php
        $query = "select * from `visible_table` where `visible`=1";
        $result = mysql_query($query);
        $first_table = '';
        while($row = mysql_fetch_array($result))
        {
            if ($first_table=='')
                $first_table = $row['id'];
            echo ('<option value='.$row['id'].'>'.$row['table'].'</option>');
        }
      ?>
  </select>
  <div id="hr"></div>
  Поле:
  <select id="field" name="field" class="txt block">
      <?php
        $table_name = db_field_value("visible_table", "table", "`id`=".$first_table);
        $query = "SHOW COLUMNS from `".$table_name."`";
        $result = mysql_query($query);
        while($row = mysql_fetch_array($result))
        {
            echo ('<option value='.$row['Field'].'>'.$row['Field'].'</option>');
        }
      ?>
  </select>
  <div id="hr"></div>
  Заголовок:
  <input type="text" id="caption" name="caption" class="txt block"/><div id="hr"></div>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Добавить</button>
  </div>
</form>
<?php
  dd_formc ();
?>
