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

dd_formo('title=Новый опрос;');
?>
<script language="JavaScript" type="text/javascript">
  function check(frm) {
    var contest_name = qtrim(getElementById ('vopros').value);
    
    if (contest_name == '') {
      alert("Поле \"Вопрос\" обязательно для заполнения");
      return;
    }
    
    frm.submit ();
  }
</script>
<div>
    <?php
    global $shag, $typeopr, $id, $row, $num, $otvet, $zagol;
    if ($shag=="2") {
        $nums=trim($num);
        $num=$nums;
        if ($nums<"2"  or !$nums or $nums=="" or $nums==" ") {
            echo "Число вариантов, которое Вы ввели является неправильным.";
        } else {
            //echo "В вашем опросе будет $nums вариантов<BR>";
            print ('<form action=?shag=3 method=post>
                        <table class="clear" width="100%">
                            <tr><td width="150px" style="padding: 0 2px;">
                            Введите название опроса <span class="error">*</span></td>
                            <td style="padding: 0 2px;" class="txt block"><input type=text name=zagol></td></tr>');
            while ($nums!=0) {
                print ('<tr>
                            <td width="150px" style="padding: 0 2px;">Вариант ответа </td>
                            <td style="padding: 0 2px;" class="txt block">
                                <input type=text name=otvet[$nums]>
                            </td></tr>');
                $nums--;
            }
            print ('<tr>
                        <td width="150px" style="padding: 0 2px;">Варианты ответов:</td>
                        <td style="padding: 0 2px;">
                            Одиночный <input type=radio name=typeopr value=1 checked="checked">
                            Множественный <input type=radio name=typeopr value=2>
                            <input type=hidden name=num value=$num><br>
                        </td></tr></table>
                        <input type=submit value=Продолжить>
                  </form>'); 
        } 
    }
    else if ($shag=="3") {
        print 'test'.$num;
        $numers=$num;
        print 'test'.$typeopr;
        if ($typeopr=="1" or $typeopr=="2") {
            print 'test'.$numers;
            while ($numers!="0") {
                print $otvet[$numers];
                $otvet[$numers]=trim($otvet[$numers]);
                if ($otvet[$numers] == "" or $otvet[$numers] == " ") 
                    {   } 
                else {
                    $otv[]=$otvet[$numers];
                }
                $numers--;
            }
            print count($otv);
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
    else {
        print ('Введите кол-во ответов, которое вы хотите использовать в своём опросе:<br>');
        print ('<form action=?shag=2 method=post><input type=text size=2 name=num value=0>
                <input type=submit value=Продолжить>
              </form>'); 
    }
    ?>
  <!--
  <form action=".?action=create&page=<?=$page?>" method="POST" onsubmit="check(this); return false;">
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
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
        <tr><td width="30%" style="padding: 0 2px;">
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
        <tr><td width="30%" style="padding: 0 2px;">
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
        <tr><td width="30%" style="padding: 0 2px;">
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
        <tr><td width="30%" style="padding: 0 2px;">
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
        <tr><td width="30%" style="padding: 0 2px;">
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
      <button class="submitBtn block" type="submit">Сохранить</button>
    </div>
  </form>
</div>-->
<?php
dd_formc ();
?>
