<?php 
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * IPC handlers
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $IFACE;

  if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
    print ('HACKERS?');
    die;
  }

  if ($_ipc_included_ != '#ipc_Included#') {
    $_ipc_included_ = '#ipc_Included#';
    $ipc_functions = array ();

    function ipc_check_path_exists () {
      global $cpath;
      if (dir_exists (config_get ('site-root').config_get ('document-root').
                      '/'.$cpath)) {
        print ('+OK');
      } else {
        print ('-ERR');
      }
    }

    function ipc_check_login () {
      global $login, $skipId;

      if ($skipId == '') {
        $skipId = -1;
      }

      if (user_registered_with_login ($login, $skipId)) {
        print ('-ERR');
      } else {
        print ('+OK');
      }
    }

    function ipc_find_regions(){
      global $country, $skipId;

      if ($skipId == '') {
        $skipId = -1;
      }

      $r = responsible_get_by_id(user_id());
      $sc = school_get_by_id($r['school_id']);

      $query = "select * from `region` where `country_id`=".$country;
      $result = db_query($query);

      //FIXME such way, because the code below doesn't work in IE (same in login/profile/info/school/data.php
      while($rows = mysql_fetch_array($result, MYSQL_ASSOC)){
          if ($rows['id']==$sc['region_id'])
            $regions.=$rows["id"].','.$rows["name"].',true;';
          else
            $regions.=$rows["id"].','.$rows["name"].',false;';
      }
      $regions.='-1,Другой,false';
      /*
      while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
        if ($rows['id']==$sc['region_id'])
          $regions .= '<option selected="true" value="'.$rows["id"].'">'.$rows["name"].'</option> ';
        else
          $regions .= '<option value="'.$rows["id"].'">'.$rows["name"].'</option> ';
      $regions .='<option value="-1">Другой</option>';*/
      print ($regions);
    }

    function ipc_find_areas(){
      global $region, $skipId;

      if ($skipId == '') {
        $skipId = -1;
      }

      $r = responsible_get_by_id(user_id());
      $sc = school_get_by_id($r['school_id']);

      $query = "select * from `area` where `region_id`=".$region;
      $result = db_query($query);

      while($rows = mysql_fetch_array($result, MYSQL_ASSOC)){
          if ($rows['id']==$sc['area_id'])
            $areas.=$rows["id"].','.$rows["name"].',true;';
          else
            $areas.=$rows["id"].','.$rows["name"].',false;';
      }
      $areas.='-1,Другой,false';
      print ($areas);
    }

    function ipc_find_cities(){
      global $region, $area, $city_status, $skipId;

      if ($skipId == '') {
        $skipId = -1;
      }

      $r = responsible_get_by_id(user_id());
      $sc = school_get_by_id($r['school_id']);

      if ($area!=NULL && $area>0)
        $query = 'select * from `city` where `area_id`='.$area.' and (`status_id` IS NULL or `status_id`='.$city_status.')';
      else if ($region!=NULL && $region>0)
        $query = 'select * from `city` where `region_id`='.$region.' and (`status_id` IS NULL or `status_id`='.$city_status.') and (`area_id` IS NULL or `area_id`=-1)';
      else
        $query = 'select * from `city` where `status_id`=NULL or `status_id`='.$city_status;
      $result = db_query($query);

      while($rows = mysql_fetch_array($result, MYSQL_ASSOC)){
          if ($rows['id']==$sc['city_id'])
            $cities.=$rows["id"].','.$rows["name"].',true;';
          else
            $cities.=$rows["id"].','.$rows["name"].',false;';
      }
      $cities.='-1,Другой,false';
      print ($cities);
    }



    function ipc_check_email () {
      global $email, $skipId;

      if ($skipId == '') {
        $skipId = -1;
      }

      $user_info = user_info_by_id (user_id ());
      if ($email == config_get ('null-email') &&
          $user_info['email'] != $email && !user_access_root ()) {
        print ('-ERR');
      } else {
        if (user_registered_with_email ($email, $skipId)) {
          print ('-ERR');
        } else {
          print ('+OK');
        }
      }
    }

    function ipc_check_wiki_node () {
      global $cpath, $skipId, $pid;

      if ($skipId == '') {
        $skipId = -1;
      }

      if (wiki_content_present_in_node ($pid, $cpath, $skipId)) {
        print ('-ERR');
      } else {
        print ('+OK');
      }
    }

    function ipc_register_function ($name, $entry) {
      global $ipc_functions;
      $ipc_functions[$name] = array ('entry' => $entry);
    }

    function ipc_initialize () {
      ipc_register_function ('check_login',       ipc_check_login);
      ipc_register_function ('check_email',       ipc_check_email);
      ipc_register_function ('check_wiki_node',   ipc_check_wiki_node);
      ipc_register_function ('check_path_exists', ipc_check_path_exists);
      ipc_register_function('find_regions', ipc_find_regions);
      ipc_register_function('find_areas', ipc_find_areas);
      ipc_register_function('find_cities', ipc_find_cities);
    }

    function ipc_exec ($func) {
      global $ipc_functions;
      if (isset ($ipc_functions[$func]) &&
          function_exists ($ipc_functions[$func]['entry'])) {
        $ipc_functions[$func]['entry'] ();
      }
    }
  }
?>
