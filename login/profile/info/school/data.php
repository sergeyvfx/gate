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

global $DOCUMENT_ROOT, $redirect, $action, $config_get;
include $DOCUMENT_ROOT . '/login/profile/inc/menu.php';
include '../menu.php';
$profile_menu->SetActive('info');
$info_menu->SetActive('school');
$err_string='';

$r = responsible_get_by_id(user_id());
$sc = school_get_by_id($r['school_id']);

if ($action == 'save') {
  global $name, $school_status, $zipcode, $country, $country_name, $region, $region_name, $area, $area_name, $city_status, $city, $city_name, $street, $house, $building, $flat, $comment;
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
  $comment = stripslashes($comment);

  $arr = array();
  
  //TODO Add check of all necessary
  if ($name!='')
    $arr['name'] = db_string($name);
  else
    $err_string='Название';
  if ($school_status!='')
    $arr['status_id'] = $school_status;
  else
    $err_string=$err_string==''?'Статус учебного заведения':$err_string.', Статус учебного заведения';
  if ($zipcode!='')
    $arr['zipcode'] = db_string($zipcode);
  else
    $err_string=$err_string==''?'Индекс':$err_string.', Индекс';
  
  if ($country>0)
    $arr['country_id'] = (int)$country;
  else if ($country_name!='')
  {
      $country_name = stripslashes($country_name);
      $country_fields = array();
      $country_fields['name'] = db_string($country_name);
      db_insert('country', $country_fields);
      $arr['country_id'] = (int)db_max('country', 'id');
  }
  else
    $err_string=$err_string==''?'Страна':$err_string.', Страна';

  if ($region>0)
    $arr['region_id'] = (int)$region;
  else if ($region_name!='')
  {
      $region_name = stripslashes($region_name);
      $region_fields = array();
      $region_fields['name'] = db_string($region_name);
      db_insert('region', $region_fields);
      $arr['region_id'] = (int)db_max('region', 'id');
  }
  else
    $err_string=$err_string==''?'Регион':$err_string.', Регион';

  if ($area>0)
    $arr['area_id'] = (int)$area;
  else if ($area_name!='')
  {
      $area_name = stripslashes($area_name);
      $area_fields = array();
      $area_fields['name'] = db_string($area_name);
      db_insert('area', $area_fields);
      $arr['area_id'] = (int)db_max('area', 'id');
  }

  if ($city>0)
    $arr['city_id'] = (int)$city;
  else if ($city_name!='')
  {
      $city_name = stripslashes($city_name);
      $city_fields = array();
      $city_fields['name'] = db_string($city_name);
      db_insert('city', $city_fields);
      $arr['city_id'] = (int)db_max('city', 'id');
      $city = $arr['city_id'];
  }
  else
    $err_string=$err_string==''?'Населенный пункт':$err_string.', Населенный пункт';

  if ($street!='')
    $arr['street'] = db_string($street);
  else
    $err_string=$err_string==''?'Улица':$err_string.', Улица';
  if ($house!='')
    $arr['house'] = db_string($house);
  else
    $err_string=$err_string==''?'Дом':$err_string.', Дом';
  $arr['building'] = db_string($building);
  $arr['flat'] = db_string($flat);
  $arr['comment'] = db_string($comment);

  //save info about school
  if ($err_string=='')
  {
    if ($r['school_id']!='' && $r['school_id']!=-1)
        db_update('school', $arr, '`id`=' . $sc['id']);
    else
    {
        db_insert('school', $arr);
        $arr=array();
        $arr['school_id'] = (int)db_max('school', 'id');
        db_update('responsible', $arr, '`user_id`='.$r['user_id']);
    }

    //save city status
    $arr= array();
    $arr['status_id'] = $city_status;
    if (count($arr) > 0)
        db_update('city', $arr, '`id`=' . $city);
  }
}


$r = responsible_get_by_id(user_id());
$sc = school_get_by_id($r['school_id']);
$cit = city_get_by_id($sc['city_id']);

//find all school statuses
$query = "select * from `school_status`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($rows['id']==$sc['status_id'])
        $statuses .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
    else
        $statuses .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';

$f = new CVCForm ();
$f->Init('', 'action=.?action\=save' . (($redirect != '') ? ('&redirect=' . prepare_arg($redirect) . ';backlink=' . prepare_arg($redirect)) : ('')) . ';method=POST;add_check_func=check;');

if ($r['school_id']!='' && $r['school_id']!=-1)
{
    $href_string = "<?=config_get('document-root');?>/tipsling/team/my";
    $f->AppendCustomField(array('src' => '<div style="padding-bottom:10px">Зарегистрировать команды вы можете <a href="'.$href_string.'">здесь</a></div>'));
    }

$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Название: <span class="error">*</span></td><td><input id="name" name="name" type="text" class="txt block" value="' . htmlspecialchars($sc['name']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Статус учебного заведения: <span class="error">*</span></td><td><select id="school_status" name="school_status" class="txt block">'.addslashes($statuses).'</select></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Почтовый индекс: <span class="error">*</span></td><td><input id="zipcode" name="zipcode" type="text" class="txt block" value="' . htmlspecialchars($sc['zipcode']) . '"></td></tr></table>'));

//find all countries
$query = "select * from `country`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($rows['id']==$sc['country_id'])
        $countries .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
    else
        $countries .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
if ($countries!=''){
    $countries .='<option value="-1">Другая</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Страна: <span class="error">*</span></td><td><select id="country" name="country" class="block" onchange="other_country()">'.addslashes($countries).'</select></td></tr><tr><td width="30%"></td><td><div id="other_country" name="other_country" style="display: none; margin-top:3px"></div></td><tr></table>'));
} else {
    $countries .='<option value="-1">Другая</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Страна: <span class="error">*</span></td><td><select id="country" name="country" class="block" onchange="other_country()">'.addslashes($countries).'</select></td></tr><tr><td width="30%"></td><td><div id="other_country" name="other_country" style="display: block; margin-top:3px"><input id="country_name" name="country_name" type="text" class="txt block"></div></td><tr></table>'));
}

//find all region
$query = "select * from `region`";
$result = db_query($query);
//TODO: Добавить фильтрацию списка (основная проблема с фильтрацией после смены значения в верхнем комбике
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    //if ($rows['country_id']==$cntr['id'])
        if ($rows['id']==$sc['region_id'])
            $regions .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
        else
            $regions .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
if ($regions!='') {
    $regions .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Регион: <span class="error">*</span></td><td><select id="region" name="region" class="block" onchange="other_region()">'.addslashes($regions).'</select></td></tr><tr><td width="30%"></td><td><div id="other_region" name="other_region" style="display: none; margin-top:3px"></div></td><tr></table>'));
} else {
    $regions .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Регион: <span class="error">*</span></td><td><select id="region" name="region" class="block" onchange="other_region()">'.addslashes($regions).'</select></td></tr><tr><td width="30%"></td><td><div id="other_region" name="other_region" style="display: block; margin-top:3px"><input id="region_name" name="region_name" type="text" class="txt block"></div></td><tr></table>'));
}
$query = "select * from `area`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    //if ($rows['country_id']==$ar['id'])
        if ($rows['id']==$sc['area_id'])
            $areas .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
        else
            $areas .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
if ($areas!=''){
    $areas .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Район: </td><td><select id="area" name="area" class="block" onchange="other_area()">'.addslashes($areas).'</select></td></tr><tr><td width="30%"></td><td><div id="other_area" name="other_area" style="display: none; margin-top:3px"></div></td><tr></table>'));
} else {
    $areas .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Район: </td><td><select id="area" name="area" class="block" onchange="other_area()">'.addslashes($areas).'</select></td></tr><tr><td width="30%"></td><td><div id="other_area" name="other_area" style="display:block; margin-top:3px"><input id="area_name" name="area_name" type="text" class="txt block"></div></td><tr></table>'));
}

$query = "select * from `city_status`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($rows['id']==$cit['status_id'])
        $city_statuses .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
    else
        $city_statuses .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Статус населенного пункта: <span class="error">*</span></td><td><select id="city_status" name="city_status" class="block" onchange="other_city_status()">'.addslashes($city_statuses).'</select></td></tr></table>'));

//find all cities
$query = "select * from `city`";
$result = db_query($query);
//TODO: Добавить фильтрацию списка (основная проблема с фильтрацией после смены значения в верхнем комбике
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    //if ($rows['country_id']==$cntr['id'])
        if ($rows['id']==$sc['city_id'])
            $cities .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
        else
            $cities .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
if ($cities!='')
{
    $cities .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Населенный пункт: <span class="error">*</span></td><td><select id="city" name="city" class="block" onchange="other_city()">'.addslashes($cities).'</select></td></tr><tr><td width="30%"></td><td><div id="other_city" name="other_city" style="display: none; margin-top:3px"></div></td><tr></table>'));
}
else
{
    $cities .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Населенный пункт: <span class="error">*</span></td><td><select id="city" name="city" class="block" onchange="other_city()">'.addslashes($cities).'</select></td></tr><tr><td width="30%"></td><td><div id="other_city" name="other_city" style="display:block; margin-top:3px"><input id="city_name" name="city_name" type="text" class="txt block"></div></td><tr></table>'));
}

$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Улица: <span class="error">*</span></td><td><input id="street" name="street" type="text" class="txt block" value="' . htmlspecialchars($sc['street']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Дом: <span class="error">*</span></td><td><input id="house" name="house" type="text" class="txt block" value="' . htmlspecialchars($sc['house']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Корпус:</td><td><input id="building" name="building" type="text" class="txt block" value="' . htmlspecialchars($sc['building']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Квартира:</td><td><input id="flat" name="flat" type="text" class="txt block" value="' . htmlspecialchars($sc['flat']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Примечание:</td><td><input id="comment" name="comment" type="text" class="txt block" value="' . htmlspecialchars($sc['comment']) . '"></td></tr></table>'));

if ($err_string!='')
    $f->AppendCustomField(array('src' => '<div class="txt error">Вы не заполнили следующие обязательные поля: '.stripslashes($err_string).'</div>'));
?>


<script type="text/JavaScript"  language="JavaScript">
  function other_country() {
    var id = getElementById("country").value;
    var opt = getElementById("other_country");
    if (id == -1) {
      opt.style.display = 'block';
      opt.innerHTML = '<input id="country_name" name="country_name" type="text" class="txt block">'
    } else {
      opt.style.display = 'none';
    }
  }

  function other_region() {
    var id = getElementById("region").value;
    var opt = getElementById("other_region");
    if (id == -1) {
      opt.style.display = 'block';
      opt.innerHTML = '<input id="region_name" name="region_name" type="text" class="txt block">'
    } else {
      opt.style.display = 'none';
    }
  }

  function other_area() {
    var id = getElementById("area").value;
    var opt = getElementById("other_area");
    if (id == -1) {
      opt.style.display = 'block';
      opt.innerHTML = '<input id="area_name" name="area_name" type="text" class="txt block">'
    } else {
      opt.style.display = 'none';
    }
  }

  function other_city() {
    var id = getElementById("city").value;
    var opt = getElementById("other_city");
    if (id == -1) {
      opt.style.display = 'block';
      opt.innerHTML = '<input id="city_name" name="city_name" type="text" class="txt block">'
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
