<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Different utilities
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

  if ($_WT_util_included_ != '###WT_UTIL_Inclided###') {
    $_WT_util_included_ = '###WT_UTIL_Inclided###';

    function WT_errors_string ($errors, $force,
                               $first_abort = false, $summary = '') {
      global $WT_errors;
      $ok = true;

      if ($force != '') {
        return WT_ForceStatusDesc ($force);
      }

      $arr = explode (' ', $errors);
      if ($summary == 'CE') {
        $res = $WT_errors['CE'];
        $ok=false;
      } else if ($summary == 'CR') {
        $res = $WT_errors['CR'];
        $ok = false;
      } else if ($errors!='') {
        for ($i = 0; $i < count ($arr); $i++) {
          if ($first_abort && $arr[$i] == 'OK') {
            continue;
          }

          if ($res != '') {
            $res .= '<br>';
          }

          $ok = false;
          $res .= $WT_errors[$arr[$i]];

          if ($first_abort && $arr[$i] != 'OK' && $arr[$i] != 'CR') {
            $res .= ' на тесте #'.($i + 1);
            break;
          }
        }
      }

      if ($first_abort && $ok) {
        $res = $WT_errors['OK'];
      }

      return $res;
    }

    function __wt_ipc_get_arg ($link, $arg) {
      if (preg_match ('/.*'.$arg.'\=([A-Za-z0-9_@\-\%\$]*).*/', $link)) {
        return preg_replace ('/.*'.$arg.'\=([A-Za-z0-9_@\-\%\$]*).*/', '\1',
                             $link);
      }

      return '';
    }

    function WT_validate_ipc_link ($link) {
      $a = urldecode ($link);
      $root = config_get ('document-root');
      if (preg_match ('/^'.prepare_pattern ($root).'\/tester\/\?ipc/', $a)) {
        $cmd = __wt_ipc_get_arg ($a, 'ipc');

        if ($cmd == 'get_problem_desc') {
          $r = __wt_ipc_get_arg ($a, 'backlink');
          return urlencode ($root.'/tester/?page=problems&act=view&id='.
                              __wt_ipc_get_arg ($a, 'id').
                            (($r != '') ? ('&redirect='.$r) : ('')));
        }

        return '';
      }

      return $link;
    }

    function WT_ForceStatusDesc ($status) {
      if ($status == 'ML') {
        return 'Установлено превышение предела памяти';
      }
    }

    function WT_ForceStatusAffective ($status) {
      return true;
    }

    function WT_delete_solution_from_xpfs ($clause) {
      global $XPFS;
      $q = db_select ('tester_solutions', array ('id'), $clause);

      while ($r=db_row ($q)) {
        $XPFS->removeItem ('/tester/testing/'.$r['id']);
      }
    }

    function WT_colorize_checker_message ($message) {
      $s = preg_replace ('/^(WA|CR|PE)/', '<b>[<font style="color: #7f0000">\1</font>]</b>', $message);
      $s = preg_replace ('/^(OK)/', '<b>[<font style="color: #007f00">\1</font>]</b>', $s);
      return $s;
    }
  }
?>
