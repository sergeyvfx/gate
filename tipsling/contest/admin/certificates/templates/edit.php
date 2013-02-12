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

global $id, $page;
formo('title=Редактирование сертификата');

$certificate = certificate_get_by_id($id);

?>
<script language="JavaScript" type="text/javascript">
  function check(frm) {
    var certificate_name = qtrim(getElementById ('name').value);
    
    if (certificate_name == '') {
      alert("Поле \"Название\" обязательно для заполнения");
      return;
    }

    frm.submit ();
  }

  function check_frm_name() {
    var name = getElementById ('name').value;

    if (qtrim(name)=='') {
        show_msg ('name_check_res', 'err', 'Это поле обязательно для заполнения');
        return;
    }

    hide_msg('name_check_res');
  }
  
  function check_frm_for() {
    var val = getElementById ('for').value;

    if (qtrim(val)=='') {
        show_msg ('for_check_res', 'err', 'Это поле обязательно для заполнения');
        return;
    }

    hide_msg('for_check_res');
  }
  
  function insertCaption(elem_to_insert, elem_to_get_value)
  {
      var for_elem = getElementById (elem_to_insert);
      var field_elem = getElementById (elem_to_get_value);
      
      for_elem.value += "#"+field_elem.value+"#";
  }
</script>

<form action=".?action=save&id=<?= $id; ?><?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST" onsubmit="check (this); return false;">
    <table class="clear" width="100%">
        <tr><td width="110px" style="padding: 0 2px;">
                Название: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input type="text" id="name" name="name" onblur="check_frm_name ();" value="<?= $certificate['name']; ?>" class="txt block"/>
            </td>
        </tr>
    </table>
    <div id="name_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="110px" style="padding: 0 2px;">
                Генерировать для: <span class="error">*</span>
            </td>
            <td width="50%px" style="padding: 0 2px;">
                <input type="text" id="for" name="for" onblur="check_frm_for ();" value="<?= $certificate['for']; ?>" class="txt block"/>
            </td>
            <td>&nbsp</td>
            <td style="padding: 0 2px;">
                <select id="field" name="field" class="txt block">
                    <?php
                        $query = "select * from `visible_field`";
                        $result = mysql_query($query);
                        while($row = mysql_fetch_array($result))
                        {
                            echo ('<option value="'.$row['caption'].'">'.$row['caption'].'</option>');
                        }
                    ?>
                </select>
            </td>
            <td width="110px">
                <input type="button" value="Вставить" onclick="insertCaption('for','field');"/>
            </td>
        </tr>
    </table>
    <div id="for_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="110px" style="padding: 0 2px;">
                Ограничение:
            </td>
            <td style="padding: 0 2px;">
                <select id="limit" name="limit" class="txt block">
                    <option value="">нет ограничения</option>
                    <?php
                        $limit_list = limit_list();
                        foreach ($limit_list as $lim) 
                        {
                            echo ('<option value="'.$lim['id'].'"'. ($lim['id']==$certificate['limit_id']?'selected':'') .'>'.$lim['name'].'</option>');
                        }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="110px" style="padding: 0 2px;">
                Шаблон: 
            </td>
            <td style="padding: 0 2px;">
                <textarea width="100%" rows="30" id="template" name="template" class="txt block"><?php echo(htmlspecialchars(stripslashes($certificate['template']))); ?></textarea>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <table width="100%">
                    <tr width="100%">
                        <td>
                            <select id="field_for_template" name="field_for_template" class="txt block">
                            <?php
                                $query = "select * from `visible_field`";
                                $result = mysql_query($query);
                                while($row = mysql_fetch_array($result))
                                {
                                    echo ('<option value="'.$row['caption'].'">'.$row['caption'].'</option>');
                                }
                            ?>
                            </select>
                        </td>
                        <td width="70px">
                            <input type="button" value="Вставить" onclick="insertCaption('template', 'field_for_template');"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    
    <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?<?= (($page != '') ? ('&page=' . $page) : ('')); ?>');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
  </form>
          
<?php
  formc ();
?>
