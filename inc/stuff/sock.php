<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Socket manipulation module
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

  if ($_SOCK_Included_ != '##SOCK_Included##') {
    $_SOCK_Included_ = '##SOCK_Included##';

    function sock_connect ($server, $port, $timeout = 0) {
      if (!$timeout)
        @ $sock = fsockopen ($server, $port); else
        @ $sock = fsockopen ($server, $port, &$errno, &$errstr, $timeout);
      return $sock;
    }

    function sock_write ($sock, $buf, $len)    {
      @fwrite ($sock, $buf, $len);
    }

    function sock_read ($sock, $len, $all = false) {
      if (!$sock) {
        return '';
      }

      return fread ($sock, $len);
    }

    function sock_read_all  ($sock, $len, $notEmpty = false) {
      if (!$sock) {
        return '';
      }

      $s = '';
      $first = true;
      $estimated = 100;

      while (true) {
        if ($first) {
          usleep (50000);
        }

        usleep (50000);
        @ $t = fread ($sock, $len);

        if ($t == '') {
          if ($notEmpty && $first && $estimated > 0) {
            --$estimated;
            continue;
          }
          break;
        }

        $s .= $t;
        $first = false;
      }
      return $s;
    }

    function sock_set_blocking ($socket, $val) {
      @ set_socket_blocking ($socket, $val);
    }
  }
?>
