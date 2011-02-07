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
  print 'HACKERS?';
  die;
}

if (!user_authorized ()) {
  header('Location: ' . config_get('document-root') . '/login/profile');
}

if (!is_responsible(user_id())) {
  print (content_error_page(403));
  return;
}

global $DOCUMENT_ROOT, $redirect, $action;
include $DOCUMENT_ROOT . '/login/profile/inc/menu.php';
include '../menu.php';
$profile_menu->SetActive('info');
$info_menu->SetActive('school');

$r = responsible_get_by_id(user_id());
$sc = school_get_by_id($r['school_id']);

$f = new CVCForm ();
$f->Init('', 'action=.?action\=save' . (($redirect != '') ? ('&redirect=' . prepare_arg($redirect) . ';backlink=' . prepare_arg($redirect)) : ('')) . ';method=POST;add_check_func=check;');

if ($action == 'save') {
  global $name, $school_status, $zipcode, $country, $country_name, $region, $area, $city_status, $city, $street, $house, $building, $flat;
  $name = stripslashes($name);
  $school_status = stripslashes($school_status);
  $zipcode = stripslashes($zipcode);
  $country = stripslashes($country);
  $region = stripslashes($region);
  $area = stripslashes($area);
  $city_status = stripslashes($city_status);
  $city = stripslashes($city);
  $street = stripslashes($street);
  $house = stripslashes($house);
  $building = stripslashes($building);
  $flat = stripslashes($flat);

  $arr = array();

  
  //TODO Add check of all necessary
  $arr['name'] = db_string($name);
  $arr['status_id'] = $school_status;
  $arr['zipcode'] = db_string($zipcode);
  

//TODO Find and check country, region, area, city_status, city
  if ($country>0)
    $arr['country_id'] = (int)$country;
  else{
      $country_name = stripslashes($country_name);
      $country_fields = array();
      $country_fields['name'] = db_string($country_name);
      db_insert('country', $country_fields);
      $arr['country_id'] = (int)db_max('country', 'id');
  }
  
  /*$arr['region_id'] = $region;
    //$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">регион='.$region.'</td><td></td></tr></table>'));
  $arr['area_id'] = $area;
  //  $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">район='.$area.'</td><td></td></tr></table>'));
  $arr['city_id'] = $city;
  //  $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">город='.$city.'</td><td></td></tr></table>'));
*/

  $arr['street'] = db_string($street);
  $arr['house'] = db_string($house);
  $arr['building'] = db_string($building);
  $arr['flat'] = db_string($flat);
  


  /*$arr['city_status'] = db_string($city_status);
    //$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">статус города='.$city_status.'</td><td></td></tr></table>'));
  }*/

  if (count($arr) > 0) 
      db_update('school', $arr, '`id`=' . $sc['id']);
  
}

$r = responsible_get_by_id(user_id());
$sc = school_get_by_id($r['school_id']);
$st = school_status_get_by_id($sc['status_id']);
$cntr = country_get_by_id($sc['country_id']);
$reg = region_get_by_id($sc['region_id']);
$ar = area_get_by_id($sc['area_id']);
$cit = city_get_by_id($sc['city_id']);

//find all school statuses
$query = "select * from `school_status`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($rows['id']==$sc['status_id'])
        $statuses .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
    else
        $statuses .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';

//find all countries
$query = "select * from `country`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($rows['id']==$sc['country_id'])
        $countries .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
    else
        $countries .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
$countries .='<option value="-1">Другая</option>';



$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Название</td><td><input id="name" name="name" type="text" class="txt block" value="' . htmlspecialchars($sc['name']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Статус учебного заведения</td><td><select id="school_status" name="school_status" class="txt block">'.addslashes($statuses).'</select></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Почтовый индекс</td><td><input id="zipcode" name="zipcode" type="text" class="txt block" value="' . htmlspecialchars($sc['zipcode']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Страна</td><td><select id="country" name="country" class="block" onchange="foo()">'.addslashes($countries).'</select></td></tr><tr><td width="30%"></td><td><div id="other_country" name="other_country" style="display: none; margin-top:3px"></div></td><tr></table>'));

$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Район</td><td><input id="area" name="area" type="text" class="txt block" value="' . htmlspecialchars($ar['name']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Статус населенного пункта</td><td><input id="city_status" name="city_status" type="text" class="txt block" value="' . htmlspecialchars($sc['city_status']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Населенный пункт</td><td><input id="city" name="city" type="text" class="txt block" value="' . htmlspecialchars($cit['name']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Улица</td><td><input id="street" name="street" type="text" class="txt block" value="' . htmlspecialchars($sc['street']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Дом</td><td><input id="house" name="house" type="text" class="txt block" value="' . htmlspecialchars($sc['house']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Корпус</td><td><input id="building" name="building" type="text" class="txt block" value="' . htmlspecialchars($sc['building']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Квартира</td><td><input id="flat" name="flat" type="text" class="txt block" value="' . htmlspecialchars($sc['flat']) . '"></td></tr></table>'));
?>


<script type="text/JavaScript"  language="JavaScript">
  function foo() {
    var id = getElementById("country").value;
    var opt = getElementById("other_country");
    if (id == -1) {
      opt.style.display = 'block';
      opt.innerHTML = '<input id="country_name" name="country_name" type="text" class="txt block">'
    } else {
      opt.style.display = 'none';
    }
  }
</script>

<div id="snavigator"><a href="<?= config_get('document-root') . "/login/profile/" ?>">Мой профиль</a>Учебное заведение</div>
${information}
<div class="form">
  <div class="content">
    <?php
    $profile_menu->Draw();
    $info_menu->Draw();
    $f->Draw();
    ?>
  </div>
</div>
