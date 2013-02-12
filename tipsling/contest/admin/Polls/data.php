<?php
if ($PHP_SELF != '') {
  print 'HACKERS?';
  die;
}
global $current_contest;

if (!user_authorized ()) {
  header('Location: ../../../../login');
}

$it = contest_get_by_id($current_contest);
$query = arr_from_query("select * from Admin_FamilyContest ".
                   "where family_contest_id=".$it['family_id']." and ".
                   "user_id=".user_id());
if (count ($query) <= 0)
{
  print (content_error_page(403));
  return;
}

?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a><a>Администрирование</a>Опросы</div>
${information}
<div class="form">
  <div class="content">
    <?php
    include '../menu.php';
    $admin_menu->SetActive('Polls');
    global $DOCUMENT_ROOT, $action, $id;
       
    if ($action == 'create') {
      poll_create_received();
    }
    $admin_menu->Draw();
    if ($action == 'edit') {
      include 'edit.php';
    } else {
      if ($action == 'save') {
        poll_update_received($id);
      } else if ($action == 'delete') {
        poll_delete($id);
      }
      $list = poll_list();
      include 'list.php';
      include 'create_form.php';
    }
    ?>
  </div>
</div>



<?php
/*
global $page, $shag, $go, $typeopr, $id, $row, $num, $otvet, $zagol;

echo "<a href=?page=add&shag=1>Создать опрос</a><br>";
echo "<a href=?page=change&shag=1>Изменить опрос.</a><br><br>";
if ($page=="add") {
    if ($shag=="1") {
        echo "Введите кол-во ответов, которое вы хотите использовать в своём опросе:<br>";
        echo "<form action=?page=add&shag=2 method=post><input type=text size=2 name=num value=0>
                <input type=submit value=Продолжить>
              </form>"; 
    }
    if ($shag=="2") {
        $nums=trim($num);
        $num=$nums;
        if ($nums<"2"  or !$nums or $nums=="" or $nums==" ") {
            echo "Число вариантов, которое Вы ввели является неправильным.";
        } else {
            echo "В вашем опросе будет $nums вариантов<BR>";
            echo "<form action=?page=add&shag=3 method=post>
                  Введите название опроса<input type=text name=zagol><br>";
            while ($nums!=0) {
                echo "Вариант ответа <input type=text name=otvet[$nums]><br>";
                $nums--;
            }
            echo "Варианты ответов:<BR>
                  Одиночный <input type=radio name=typeopr value=1>
                  Множественный <input type=radio name=typeopr value=2>
                  <input type=hidden name=num value=$num><br>
                  <input type=submit value=Продолжить>
                  </form>"; 
        } 
    }
    if ($shag=="3") {
        $numers=$num;
        if ($typeopr=="1" or $typeopr=="2") {
            while ($numers!="0") {
                $otvet[$numers]=trim($otvet[$numers]);
                if ($otvet[$numers] == "" or $otvet[$numers] == " ") 
                    {   } 
                else {
                    $otv[]=$otvet[$numers];
                }
                $numers--;
            }
            $num_elsements=count($otv);
            if ($num_elsements<2) 
                echo "Обычно в вопросах не меньше 2 ответов"; 
            else {
                echo "Опрос:<BR>";
                echo "Вопрос: $zagol<br>";
                for ($idx=0; $idx<$num_elsements; ++$idx) {
                    echo "$otv[$idx]<BR>"; 
                }
                echo "Тип опроса:<br>";
                if ($typeopr=="1") 
                    echo "Одиночный выбор"; 
                else 
                    echo "Множественный выбор";
                $dats=time();
                echo " $dats $zagol $typeopr";
                mysql_query("INSERT INTO allvoits VALUES ( '', '$zagol', '$typeopr') ") or die("Ошибка");
                $id=mysql_insert_id();
                $num_elsements=count($otv);
                for ($idx=0; $idx<$num_elsements; ++$idx) {
                    $insert=mysql_query("INSERT INTO voit VALUES (
                                        '',
                                        '$otv[$idx]',
                                        '0',
                                        '$id') ") or die("Ошибка2"); 
                }
            }
        }
    }
}
if ($page=="change") {
    if ($shag=="1") {
        $result=mysql_query("SELECT * FROM allvoits");
        while ($row = mysql_fetch_array($result, MYSQL_NUM))
        {
            echo "<a href=\"?page=change&shag=2&id=$row[0]&name=$row[1]\">$row[1]<a><BR>";
        }
    }
    if ($shag=="2") {
        echo "<form action=?page=change&shag=3 method=post>";
        $result=mysql_query("SELECT * FROM allvoits WHERE id='$id'");
        while ($row = mysql_fetch_array($result, MYSQL_NUM))
        {
            echo "Одиночный <input type=radio name=typeopr value=1"; 
            if ($row[2]=="1") echo " checked"; echo ">";
            echo "Множественный <input type=radio name=typeopr value=2"; 
            if ($row[2]=="2") echo " checked"; echo ">";
            echo "<br>Вопрос: <input type=text name=zagol value=\"$row[1]\"><br>";
        }
        $result=mysql_query("SELECT * FROM voit WHERE idvoit='$id'");
        $nums=mysql_num_rows($result);
        $num=mysql_num_rows($result);
        while ($row = mysql_fetch_array($result, MYSQL_NUM))
        {
            echo "Вариант ответа <input type=text name=otvet[$row[0]] value=\"$row[1]\"><br>";
            echo "<input type=hidden name=id[$row[0]] value=$row[0]>";
        }
        echo "<input type=hidden name=num value=$num><br>
              <input type=hidden name=myid value=$id>
              <input type=submit value=Продолжить>
              </form>
              <br>
              <a href=?go=addotv&id=$id>Добавить вариант</a><br>
              <a href=\"?go=del&id=$id&name=$name\">Полностью удалить опрос</a>";
    }
    if ($shag=="3") {
        $numers=$num;
        if ($typeopr=="1" or $typeopr=="2") {
            $kolvootvall=mysql_query("SELECT * FROM voit");
            $kolvootvall=mysql_num_rows($kolvootvall);
            $i=$kolvootvall;
            while ($i!="0") {
                if ($otvet[$i] != "" or $otvet[$i] != " ") {
                    $otvet[$i]=trim($otvet[$i]);
                    $otv[$i]=$otvet[$i];
                }
                $i--;
            }
            $num_elsements=count($otv);
            if ($num_elsements<2) 
                echo "Обычно в опросах не меньше 2 ответов"; 
            else {
                echo "Опрос:<BR>";
                echo "Вопрос: $zagol<br>";
                echo "Тип опроса:<br>";
                if ($typeopr=="1") echo "Одиночный выбор"; 
                else echo "Множественный выбор";
                $query = "update allvoits set vopros='$zagol', type='$typeopr' WHERE id='$myid'";
                mysql_query($query, $link) or die("Ошибка при изменении");
            }
            $kolvootvall=mysql_query("SELECT * FROM voit");
            $kolvootvall=mysql_num_rows($kolvootvall);
            $id=$kolvootvall;
            while ($id!="0") {
                if ($otv[$id]!="") {
                    echo "$otv[$id] $id<BR>";
                    mysql_query("update voit set text='$otv[$id]' WHERE id='$id'") or die("Ошибка при изменении");
                }
                $id--;
            }
        } 
    }  
}
if ($go=="addotv") {
    if ($shags=="ok") {
        mysql_query("INSERT INTO voit VALUES (
                        '',
                        '$addotvet',
                        '0',
                        '$id') ") or die("Ошибка2");
            echo "Добавленно";
    }
    echo "<form actiom=?page=addotv&shags=ok method=post>
          <input type=text name=addotvet>
          <input type=hidden name=id value=$id>
          <input type=hidden name=shags value=ok>
          <input type=submit value=Добавить><br>";
}
if ($go=="del") {
    echo "Удаление опроса, $name <br> Вы точно этого хотите? <br>
         <a href=?go=dellok&id=$id>Да</a> <a href=>нет</a>";
}
if ($go=="dellok") {
    mysql_query("DELETE FROM allvoits WHERE id='$id'") or die ("Ошибка при удалении");
    echo "Удалено";
}
echo "<br>&copy; Програмирование <a href=http://webstyle.biz>WEBstyle.BIZ</a><BR>
Скрипт WSvoit v 0.2";
 */
?>