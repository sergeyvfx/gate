<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Set of built-in functions
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

  if ($_builtin_included_ != '#builtin_Included#') {
    $_builtin_included_ = '#builtin_Included#';

    $days = array ('Mon' => 'Понедельник',
                   'Tue' => 'Вторник',
                   'Wed' => 'Среда',
                   'Thu' => 'Четверг',
                   'Fri' => 'Пятница',
                   'Sat' => 'Суббота',
                   'Sun' => 'Воскресенье');

    $months  = array ('01' => 'Январь', '02' => 'Февраль', '03' => 'Март',
                      '04' => 'Апрель', '05' => 'Май', '06' => 'Июнь',
                      '07' => 'Июль', '08' => 'Август', '09' => 'Сентябрь',
                      '10' => 'Октябрь', '11' => 'Ноябрь', '12' => 'Декабрь');

    $months2 = array ('01' => 'Января', '02' => 'Февраля', '03' => 'Марта',
                      '04' => 'Апреля', '05' => 'Мая', '06' => 'Июня',
                      '07' => 'Июля', '08' => 'Августа', '09' => 'Сентября',
                      '10' => 'Октября', '11' => 'Ноября', '12' => 'Декабря');

    $browser_array = 0;

    function println ($text) { print ($text."\n"); }

    function atoi ($s)    {
      if (isnumber ($s)) {
        return $s;
      }
      return 0;
    }

    function mtime () {
      list ($sec, $msec) = explode (' ', microtime ());
      return $sec+$msec;
    }

    function inarr ($arr, $item) {
      for ($i = 0; $i < count ($arr); $i++) {
        if ($arr[$i] == $item) {
          return true;
        }
      }

      return false;
    }

    function unserialize_params ($s) {
      if ($s[strlen ($s)] != ';') {
        $s .= ';';
      }

      $i = 0;
      $n = strlen ($s);
      $token = $valiable = $value = '';
      $result = array ();

      while ($i < $n) {
        $c = $s[$i];

        if ($c == '=') {
          $variable = $token;
          $token = '';
        } else if ($c == ';') {
          if ($variable != '') {
            $result[$variable] = $token;
          }
          $variable = $token = '';
        } else if ($c=="\\") {
          $c = $s[++$i];
          if ($c == 'n') $token .= "\n"; else
          if ($c == 'r') $token .= "\r"; else
          if ($c == 't') $token .= "\t"; else $token.=$c;
        } else {
          $token.=$c;
        }

        $i++;
      }

      if ($variable != '') {
        $result[$variable] = $value;
      }

      return $result;
    }

    function combine_arrays ($a, $b) {
      $res = array ();

      if (is_array ($a)) {
        foreach ($a as $k => $v) {
          $res[$k] = $v;
        }
      }

      if (is_array ($b)) {
        foreach ($b as $k => $v) {
          $res[$k] = $v;
        }
      }

      return $res;
    }

    function swriteln ($s, $w) { return $s.$w."\n"; }

    function arr_from_ret_query ($q, $field = '') {
      $arr = array ();

      while ($r = db_row ($q)) {
        if ($field == '') {
          $arr[] = $r;
        } else {
          $arr[] = $r[$field];
        }
      }

      return $arr;
    }

    function arr_from_query ($query, $field = '') {
      $q = db_query ($query);
      return arr_from_ret_query ($q, $field);
    }

    function isnumber ($a, $signed = false) {
      if ($signed) {
        return preg_match ('/^([\+\-])?[0-9]+$/', $a);
      }

      return preg_match ('/^[0-9]+$/', $a);
    }

    function isalphanum ($s)    { return preg_match ('/^[0-9A-Za-z_]+$/', $s); }
    function check_folder ($s)  { return preg_match ('/^[0-9A-Za-z_\-]+$/', $s); }
    function check_email ($str) { return preg_match ('/^([A-Za-z0-9_\.]+)@(([A-Za-z0-9_]+\.?)+)$/', $str); }

    function check_dir ($s) {
      if (strlen ($s) == 0) {
        return false;
      }

      return preg_match ('/(\/[A-Z0-9a-z]+)+\/?/', $s);
    }

    function get_browser_cacheable () {
      global $browser_array;

      if ($browser_array == 0) {
        $browser_array = array ();
        $s = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match ('/MSIE/si', $s)) $browser_array['engine'] = 'DONKEY'; else
        if (preg_match ('/OPERA/is', $s)) $browser_array['engine'] = 'OPERA'; else
        if (preg_match ('/AppleWebKit/is', $s)) $browser_array['engine'] = 'OPERA'; else
        if (preg_match ('/GECKO/is', $s) || preg_match ('/MOZILLA/is', $s)) $browser_array['engine'] = 'GECKO';
      }

      return $browser_array;
    }

    function _get_browser   () {
      $browser = get_browser_cacheable ();

      return $browser['browser'];
    }

    function browser_engine () {
      $browser = get_browser_cacheable ();

      return $browser['engine'];
    }

    function eval_code ($code) {
      ob_start ();
      eval ('?>'.$code);
      $r = ob_get_contents ();
      ob_end_clean ();
      return $r;
    }

    function get_real_ip () {
      global $_SERVER;

      if (!empty ($_SERVER['HTTP_X_FORWARDER_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        return $_SERVER['REMOTE_ADDR'];
      }
    }

    function client_info () {
      global $HTTP_USER_AGENT;
      $browser=get_browser_cacheable ();
      return array (
        'realIP'        => get_real_ip (),
        'browserEngine' => browser_engine (),
        'userAgent'     => $_SERVER['HTTP_USER_AGENT'],
      );
    }

    function format_date ($self, $format = 'd.m.y') { return date ($format, $self); }
    function format_date_time ($self) { return date ("d.m.y H:i", $self); }

    function format_ldate ($self, $use_short_days = true) {
      global $days, $months2;

      if ($use_short_days) {
        if (date ('d-m-Y', $self) == date ('d-m-Y', time ())) return 'Сегодня';
        if (date ('d-m-Y', $self) == date ('d-m-Y', time () - 24 *60 * 60)) return 'Вчера';
        if (date ('d-m-Y', $self) == date ('d-m-Y', time () - 48 *60 * 60)) return 'Позавчера';
      }

      $day = $days[date ('D', $self)];
      $d = date ('d', $self);
      $m = $months2[date ('m', $self)];
      $y = date ('Y', $self);

      return $day.', '.$d.' '.$m.' '.$y.'г.';
    }

    function format_ltime ($self, $use_short_days = true) {
      return format_ldate ($self, $use_short_days).', '.date ('H:i:s', $self);
    }

    function char_count ($ch, $s) {
      $r = 0;

      for ($i = 0; $i < strlen ($s); $i++) {
        if ($s[$i] == $ch) {
          $r++;
        }
      }

      return $r;
    }

    function add_info ($txt) {
      global $CORE;

      if ($CORE == nil) {
        println ('<b>CORE info: </b>'.$txt);
        return;
      }

      $CORE->PAGE->AddInfo ($txt);
    }

    function get_cur_dir () {
      $abs = preg_replace ('/\/[\w\.]+$/', '', $_SERVER['SCRIPT_NAME']);
      $r = preg_replace ('/^'.prepare_pattern (config_get ('document-root')).'/', '', $abs);

      if ($r=='') {
        $r='/';
      }

      return $r;
    }

    function nav_inside ($url) {
      $url = preg_replace ('/\/+$/', '', $url);

      if ($url == '') {
        $url='/';
      }

      $url = preg_replace ('/\/+/', '/', $url);
      $dir = get_cur_dir ();

      if (!preg_match ('/^'.prepare_pattern ($url).'/', $dir)) {
        return -1;
      }

      if ($url != '/') {
        $difference = preg_replace ('/^'.prepare_pattern ($url).'/', '', $dir);
      } else {
        $difference = $url;
      }

      $difference = preg_replace ('/\/+$/', '', $difference);
      if ($difference == '') {
        return 0; // full entry
      }

      if ($difference[0] != '/') {
        return -1;
      }

      $entries = 0;

      for ($i = 0; $i < strlen ($difference); $i++) {
        if ($difference[$i] == '/') {
          $entries++;
        }
      }

      return $entries;
    }

    function check_locked () {
      global $login, $passwd;

      if (!opt_get ('site_lock')) {
        return false;
      }

      if ($login != '') {
        user_authorize ($login, $passwd);
      }

      if (user_access_root ()) {
        return false;
      }

      tplp ('common/site_lock');

      return true;
    }

    function smartcmp ($val, $cmp) {
      $rule = preg_replace ('/(^[\=\<\>]+)(.*)/', '\1', $cmp);
      $dummy = preg_replace ('/(^[\=\<\>]+)(.*)/', '\2', $cmp);

      if ($rule=='==') { if ($val==$dummy)  return 'COMPILES'; else return 'NOTCOMPILES'; } else
      if ($rule=='<')  { if ($val<$dummy)   return 'COMPILES'; else return 'EQGREATER'; } else
      if ($rule=='>')  { if ($val>$dummy)   return 'COMPILES'; else return 'EQLESS'; } else
      if ($rule=='<=') { if ($val<=$dummy)  return 'COMPILES'; else return 'GREATER'; } else
      if ($rule=='>=') { if ($val>=$dummy)  return 'COMPILES'; else return 'LESS'; }
    }

    function core_alpha ($a) {
      $arr = " ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      if ($a < 27) {
        return $arr[$a];
      } else {
        $b = $a; $tmp = 0;

        while ($b > 0) {
          $tmp++;
          $b -= 26;
        }

        return core_alpha ($tmp - 1) . $arr[$b + 26];
      }
    }

    function FullLocalTime ($t = '', $add_timezone = true) {
      if ($t == '') {
        $t=time ();
      }

      return date ('D, d M Y h:i:s '.((!$add_timezone)?(''):('+'.config_get ('time-zone'))), $t);
    }

    function Timer ($t) {
      $prefix = '';

      if ($t < 0) {
        $prefix = '-';
        $t *= -1;
      }

      $s = $t % 60;
      $m = $t / 60 % 60;
      $h = $t / 60 / 60 % 24;
      $res = sprintf ("%02d:%02d:%02d", $h, $m, $s);
      $d = floor ($t / 60 / 60 / 24);

      if ($d) {
        if ($d % 10 == 1)  $ds = ' день'; else
        if ($d % 100 == 0) $ds = 'дней'; else
        if ($d % 10 < 5)   $ds = ' дня'; else $ds = 'дней';
        return $res = $d." $ds, ".$res;
      }

      return $prefix.$res;
    }

    function swap ($a, $b) { $c = $a; $a = $b; $b = $c; }

    function crlf2br ($s) {
      return str_replace ("\n", '<br>', str_replace ("\r\n", '<br>', $s));
    }
  }
?>
