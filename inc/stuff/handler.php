<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Handlers of body messages
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

  if ($_hanlder_included_ != '#handler_Included#') {
    $_hanlder_included_ = '#handler_Included#';

    $handlers = array ();

    function handler_add ($body, $handler, $callback, $params = array ()) {
      global $handlers;
      $handlers[$body][$handler][]=array ('callback' => $callback,
                                          'params' => $params);
    }

    function add_body_handler ($handler, $callback, $params = array ()) {
      handler_add ('body', $handler, $callback, $params);
    }

    function handler_get_list ($body, $handler = '') {
      global $handlers;

      if ($handler == '') {
        return $handlers[$body];
      }

      return $handlers[$body][$handler];
    }

    function get_body_handlers ()     { return handler_get_list ('body'); }

    function handler_build_callback ($callback) {
      $res = $callback['callback'].' (';
      $printend = false;
      $params = $callback['params'];

      for ($i = 0; $i < count ($params); $i++) {
        if ($printed) {
          $res .= ', ';
        }
        $res .= $params[$i];
        $printed = true;
      }

      $res .= ')';
      return $res;
    }
  }
?>
