<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Redirector
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

  if ($_redirect_included_ != '#redirect_Included#') {
    $_redirect_included_ = '#redirect_Included#';
    $redirecting = false;
    $redirector_skip_vars = array ();
    $redirector_empty_vars = array ();

    function get_redirection ($encode = true, $overwrite = false) {
      global $redirector_skip_vars, $redirector_empty_vars;
      $result = '';

      foreach ($_GET as $k => $v) {
        if ($redirector_skip_vars[$k]['##EMPTY##'] ||
            $redirector_skip_vars[$k][$v]) {
          continue;
        }

        if (strtolower($k) == 'redirect' && !$overwrite) {
          if ($encode) {
            return urlencode ($v);
          } else {
            return $v;
          }
        } else {
          if ($v != '' || $redirector_empty_vars[$k]) {
            if ($result != '') {
              $result .= '&';
            }

            $v = urlencode ($v);
            $result .= "$k=$v";
          }
        }
      }

      $dummy = preg_replace ('/\?.*$/', '', $GLOBALS['REQUEST_URI']);
      $result = $dummy.(($result!='')?('?'.$result):(''));

      if (!$encode) {
        return $result;
      }

      return urlencode ($result);
    }

    function redirect ($url = '', $skipvars = array ()) {
      global $redirect;

      foreach ($skipvars as $k => $v) {
        redirector_add_skipvar ($k, $v);
      }

      if ($url == 'SELF') {
        $url = get_redirection (false);
      }

      if ($url != '') {
        $redirect = $url;
      }

      if ($redirect == '') {
        $redirect = config_get ('document-root');
      }

      header ("Location: $redirect");
    }

    function redirector_get_backlink () {
      global $redirect;

      if ($redirect != '') {
        return $redirect;
      }

      return config_get ('document-root');
    }

    function redirector_add_skipvar ($name, $val = '') {
      if ($val == '') {
        $val = '##EMPTY##';
      }

      global $redirector_skip_vars;
      $redirector_skip_vars[$name][$val] = true;
    }

    function redirector_add_emptyvar ($name) {
      global $redirector_empty_vars; $redirector_empty_vars[$name]=true;
    }

    function redirector_remove_skipvar ($name, $val = '') {
      if ($val == '') {
        $val = '##EMPTY##';
      }

      global $redirector_skip_vars;
      unset ($redirector_skip_vars[$name][$val]);
    }
  
    redirector_add_skipvar ('action', 'save');
    redirector_add_skipvar ('action', 'create');
    redirector_add_skipvar ('action', 'delete');
    redirector_add_skipvar ('action', 'down');
    redirector_add_skipvar ('action', 'up');
  }
?>
