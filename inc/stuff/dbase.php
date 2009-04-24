<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Database abstraction layer
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

  if ($_dbase_included_ != '#dbase_Included#') {
    $_dbase_included_ = '#dbase_Included#';

    $db_resource      = 0;
    $db_connected     = false;
    $db_query_counter = 0;
    $db_last_query    = '';
    $db_cache         = array ();

    function db_add_list_cache_item ($item, $data) {
      global $db_cache;
      $db_cache['LIST'][$item][] = $data;
    }

    function db_delete_list_cache_item ($item, $data) {
      global $db_cache;
      $n = count ($db_cache['LIST'][$item]);
      $arr = array ();

      for ($i = 0; $i < $n; $i++) {
        if ($db_cache['LIST'][$item][$i] != $data) {
          $arr[] = $db_cache['LIST'][$item][$i];
        }
      }

      $db_cache['LIST'][$item]=$arr;
    }

    function db_list ($item) {
      global $db_cache;

      if (isset ($db_cache['LIST'][$item])) {
        return $db_cache['LIST'][$item];
      }

      $arr = array ();
      $q = db_query ("SHOW $item");

      while ($r = mysql_fetch_array ($q)) {
        $arr[] = $r[0];
      }

      $db_cache['LIST'][$item] = $arr;
      return $arr;
    }

    function db_dblist       ()      { return db_list ('DATABASES'); }
    function db_tablelist    ()      { return db_list ('TABLES'); }
    function db_exists       ($name) { return inarr (db_dblist (), $name); }
    function db_table_exists ($name) { return inarr (db_tablelist (), $name); }

    function db_safecreate   ($name) {
      if (!db_exists ($name)) {
        db_query ("CREATE DATABASE `$name`");
      }
    }

    function db_connect ($create = false) {
      global $db_resource, $db_connected;

      if (!function_exists ('mysql_connect')) {
        print ('PHP not configured for using MySQL');
        exit ();
      }
      
      $db_resource = mysql_connect (config_get ('db-host'),
                                    config_get ('db-user'),
                                    config_get ('db-password'));

      if ($db_resource == 0) {
        print ('Could not connect to database');
        exit ();
      }

      db_query ('/*!40101 SET NAMES \''.config_get ('db-codepage').'\' */');

      if ($create) {
        db_safecreate (config_get ('db-name'));
      }

      $db_connected = mysql_select_db (config_get ('db-name'), $db_resource);
    }

    function db_affected () {
      global $db_resource;
      return mysql_affected_rows ($db_resource);
    }

    function db_error () {
      global $db_resource;
      return mysql_error ($db_resource);
    }

    function db_field_value ($table, $field, $clause = '', $suffix = '') {
      $r = db_row (db_query ("SELECT `$field` FROM `$table`".
                             ((trim ($clause)=='')?(''):(" WHERE $clause")).
                             (($suffix!='')?(" $suffix"):(''))));
      return $r[$field];
    }

    function db_row_value ($table, $clause = '', $suffix = '') {
      $r = db_row (db_query ("SELECT * FROM `$table`".
                             ((trim ($clause)=='')?(''):(" WHERE $clause")." $suffix")));
      return $r;
    }

    function db_last_insert () {
      $res = db_query ("SELECT LAST_INSERT_ID() as idd");
      return @mysql_result ($res, 0);
    }

    function db_func ($table, $func, $par, $clause = '') {
      $sql = "SELECT $func($par) AS `alias` FROM `$table`".
        ((trim ($clause)=='')?(''):(" WHERE $clause"));
      $q = db_query ($sql);

      if (db_affected () <= 0) {
        return '';
      }

      $r = db_row ($q);

      return $r['alias'];
    }

    function db_count ($table, $clause = '') {
      return db_func ($table, 'COUNT', '*', $clause);
    }

    function db_max ($table, $field, $clause = '') {
      return db_func ($table, 'MAX', "`$field`", $clause);
    }

    function db_min ($table, $field, $clause = '') {
      return db_func ($table, 'MIN', "`$field`", $clause);
    }

    function db_query ($query) {
      global $db_query_counter, $db_resource;

      $db_query_counter++;
      db_set_last_query ($query);
      /*print "## $query<br>\n";*/

      return mysql_query ($query, $db_resource);
    }

    function db_query_count () {
      global $db_query_counter;
      return $db_query_counter;
    }

    function db_row ($q)       { return mysql_fetch_assoc ($q);}
    function db_row_array ($q) { return mysql_fetch_array ($q); }

    function db_get_tables () { return db_tablelist (); }

    function db_create_table ($name, $fields) {
      if (count ($fields) == 0) {
        return false;
      }

      $list = '';
      foreach ($fields as $field => $type) {
        if ($list != '') {
          $list .= ', ';
        }
        $list .= "`$field` $type";
      }
      $r = db_query ("CREATE TABLE `$name` ($list)".
                     ((config_get ('php-version')>=5)?(' DEFAULT CHARSET="UTF8"'):('')));

      if ($r) {
        db_add_list_cache_item ('TABLES', $name);
      }

      return $r;
    }

    function db_destroy_table ($name) {
      $r = db_query ("DROP TABLE `$name`");

      if ($r) {
        db_delete_list_cache_item ('TABLES', $name);
      }

      return $r;
    }

    function db_update ($table, $arr, $clause='') {
      if (count ($arr) <= 0) {
        return;
      }

      $sql = '';
      foreach ($arr as $k => $v) {
        $sql .= (($sql!='')?(','):(''))."`$k`=".$v;
      }
      $sql = "UPDATE `$table` SET $sql".(($clause!='')?(" WHERE $clause"):(''));
      db_query ($sql);
    }

    function db_select ($table, $arr = array ('*'), $clause = '', $suffix = '') {
      if (count ($arr) <= 0) {
        return;
      }

      $sql = '';
      foreach ($arr as $f) {
        $sql .= (($sql!='')?(','):('')).(($f!='*')?("`$f`"):('*'));
      }

      $sql = "SELECT $sql FROM `$table` ".
        (($clause!='')?(" WHERE $clause"):('')).' '.$suffix;

      return db_query ($sql);
    }

    function db_delete ($table, $clause = '') {
      $sql = "DELETE  FROM `$table`".(($clause!='')?(" WHERE $clause"):(''));
      db_query ($sql);
    }

    function db_insert ($table, $arr) {
      if (count ($arr) <= 0) {
        return;
      }

      $fields = '';
      $values = '';

      foreach ($arr as $k => $v) {
        if ($fields != '') {
          $fields .= ',';
        }

        if ($values != '') {
          $values .= ',';
        }

        $fields .= "`$k`";
        $values .= $v;
      }

      $sql = "INSERT INTO `$table` ($fields) VALUES($values)";

      db_query ($sql);
    }

    function db_swap_values ($table, $id1, $id2, $field, $idfield = 'id') {
      $r = db_row (db_query ("SELECT `$field` FROM `$table` ".
                             "WHERE `$idfield`=$id1"));
      $v1 = $r[$field];

      $r = db_row (db_query ("SELECT `$field` FROM `$table` ".
                             "WHERE `$idfield`=$id2"));
      $v2=$r[$field];

      db_update ($table, array ($field=>"\"$v2\""), "`$idfield`=$id1");
      db_update ($table, array ($field=>"\"$v1\""), "`$idfield`=$id2");

      return true;
    }
  
    function db_move_up ($table, $id, $clause = '',
                         $idfield = 'id', $orderfield = 'order') {
      $order = db_field_value ($table, $orderfield, "`$idfield`=$id");

      if ($order == '') {
        return false;
      }

      $max = db_max ($table, $orderfield, "`$orderfield`<$order".
                     ((trim ($clause)!='')?(' AND '.$clause):('')));

      if ($max == '') {
        return false;
      }

      $prev = db_field_value ($table, $idfield, "`$orderfield`=$max".
                              ((trim ($clause)!='')?(' AND '.$clause):('')));

      if ($prev == '') {
        return false;
      }

      return db_swap_values ($table, $id, $prev, $orderfield, $idfield);
    }  

    function db_move_down ($table, $id, $clause = '',
                           $idfield = 'id', $orderfield = 'order') {
      $order = db_field_value ($table, $orderfield, "`$idfield`=$id");

      if ($order == '') {
        return false;
      }

      $min = db_min ($table, 'order', "`$orderfield`>$order".
                     ((trim ($clause)!='')?(' AND '.$clause):('')));

      if ($min == '') {
        return false;
      }

      $next = db_field_value ($table, $idfield, "`$orderfield`=$min".
                              ((trim ($clause)!='')?(' AND '.$clause):('')));

      if ($next == '') {
        return false;
      }

      return db_swap_values ($table, $id, $next, $orderfield,$idfield);
    }

    function db_create_table_safe ($name, $fields) {
      if (!db_table_exists ($name)) {
        db_create_table ($name, $fields);
      }
    }

    function db_next_field ($table, $field, $clause = '') {
      return db_max ($table, $field, (trim ($clause)!='')?($clause):('')) + 1;
    }

    function db_next_order ($table, $clause = '') {
      return db_next_field ($table, 'order', $clause);
    }

    function db_set_last_query ($sql) {
      global $db_last_query;
      $db_last_query = $sql;
    }

    function db_last_query () {
      global $db_last_query;
      return $db_last_query;
    }

    function db_html_string ($s) {
      return '"'.addslashes (htmlspecialchars ($s)).'"';
    }

    function db_string ($s) {
      return '"'.addslashes ($s).'"';
    }
  }
?>
