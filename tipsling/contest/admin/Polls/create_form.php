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
    var contest_name = qtrim(getElementById ('zagol').value);
    
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
            print ('<form action="?action=create" method=post>
                        <table class="clear" width="100%">
                            <tr><td width="150px" style="padding: 0 2px;">
                            Введите название опроса <span class="error">*</span></td>
                            <td style="padding: 0 2px;" class="txt block"><input type=text name=zagol></td></tr>');
            while ($nums!=0) {
                print ('<tr>
                            <td width="150px" style="padding: 0 2px;">Вариант ответа </td>
                            <td style="padding: 0 2px;" class="txt block">
                                <input type=text name=otvet'.$nums.'>
                            </td></tr>');
                $nums--;
            }
            print ('<tr>
                        <td width="150px" style="padding: 0 2px;">Варианты ответов:</td>
                        <td style="padding: 0 2px;">
                            Одиночный <input type=radio name=typeopr value=1 checked="checked">
                            Множественный <input type=radio name=typeopr value=2>
                            <input type=hidden name=num value='.$num.'><br>
                        </td></tr></table>
                        <input type=submit value=Создать>
                  </form>'); 
        } 
    }
    else {
        print ('Введите кол-во ответов, которое вы хотите использовать в своём опросе:<br>');
        print ('<form action=?shag=2 method=post><input type=text size=2 name=num value=0>
                <input type=submit value=Продолжить>
              </form>'); 
    }
    ?>
</div>
<?php
dd_formc ();
?>
