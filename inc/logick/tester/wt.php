<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Backend for WebTester Server
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

  if ($_WT_included_ != '###WT_Inclided###') {
    $_WT_util_included_ = '###WT_Inclided###';

    class WebTester {
      var $sock=-1;
      var $host, $port;

      function OptGet ($opt, $cfg) {
        $val = opt_get ($opt);

        if ($val == '') {
          $val = config_get ($cfg);
        }

        return $val;
      }

      function WebTester () {
        $this->CreateSettings ();

        $this->host=$this->OptGet ('WT_Server_Host', 'WT-Server');
        $this->port=$this->OptGet ('WT_Server_Port', 'WT-Port');
      }

      function CreateSettings () {
        global $XPFS;

        $cfg=db_unpack ($XPFS->readFile ('/tester/wt.cfg'));

        if ($cfg['initialized']) {
          return;
        }

        manage_settings_create ('Имя хоста основного сервера WebTester',
                                'WebTester', 'WT_server_host',  'CSCText');
        manage_settings_create ('IPC-порт основного сервера WebTester', 
                                'WebTester', 'WT_server_port',  'CSCNumber');
        manage_settings_create ('Логин на основной сервер WebTester',
                                'WebTester', 'WT_server_login', 'CSCText');
        manage_settings_create ('Пароль на основной сервер WebTester',
                                'WebTester', 'WT_server_pass',  'CSCPassword');

        manage_setting_use ('WT_server_host');
        manage_setting_use ('WT_server_port');
        manage_setting_use ('WT_server_login');
        manage_setting_use ('WT_server_pass');

        $cfg['initialized'] = 1;
        $XPFS->createDirectory ('/', 'tester');
        $XPFS->createFile ('/tester', 'wt.cfg');
        $XPFS->writeBlock ('/tester/wt.cfg', db_pack ($cfg));
      }

      function Connect () {
        $this->sock = sock_connect ($this->host, $this->port);
        sock_set_blocking ($this->sock, false);
        $this->GetRetBuf ();
      }

      function GetRetBuf ($max_buf_len = 65536) {
        if ($this->sock < 0) {
          $this->Connect ();
        }

        $res = sock_read_all ($this->sock, $max_buf_len, true);
        return $res;
      }

      function SendCommand ($cmd, $args=array ()) {
        if ($this->sock < 0) {
          $this->Connect ();
        }

        $s = trim ($cmd);

        for ($i = 0, $n = count ($args); $i < $n; ++$i) {
          $s .= ' "'.addslashes ($args[$i]).'"';
        }

        $s .= "\n";
        sock_write ($this->sock, $s, strlen ($s));

        $r = $this->GetRetBuf ();
        return $r;
      }

      function Auth ($login, $pass) {
        return $this->SendCommand ('login', array ($login, $pass));
      }

      function AuthRoot () {
        return $this->Auth ($this->OptGet ('WT_server_login', 'WT-login'),
                            $this->OptGet ('WT_server_pass', 'WT-pass'));
      }

      function ParseBuf ($buf, $stat) {
        $space = strpos ($buf, ' ');
        $stat = substr ($buf, 0, $space);
        return substr ($buf, $space + 1, strlen ($buf) - 1 - $space);
      }
    }
  }
?>
