<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Common configuration
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

  if ($_configSet_included_ != '#configSet_Included#') {
    $_configSet_included_ = '#configSet_Included#'; 

    config_set ('proto', (($_SERVER['HTTPS']!='')?('https'):('http')));

    // Major version of php
    config_set ('php-version', preg_replace ('/^([0-9]).*/', '\1', phpversion ()));
    config_set ('check-database', true);
    config_set ('character-set', 'utf-8');
    config_set ('internal-charset', 'utf-8');
    config_set ('site-root', preg_replace ('/\/$/' ,'', $_SERVER['DOCUMENT_ROOT']));

    $tmp = preg_replace ('/^'.prepare_pattern (config_get ('site-root')).'/', '', $DOCUMENT_ROOT);

    config_set ('document-root', $tmp);
    config_set ('data-file', 'data.php');
    config_set ('wiki-index', 'data.php');

    config_set ('http-host', $_SERVER['HTTP_HOST']);
    config_set ('http-document-root', config_get ('proto').'://'.$_SERVER['HTTP_HOST'].$tmp);

    config_set ('db-host',     'localhost');
    config_set ('db-user',     'root');
    config_set ('db-password', '');
    config_set ('db-codepage', 'utf8');
    config_set ('db-name', 'gate');

    config_set ('storage-root', '/storage');
    config_set ('storage-digits', 4);
    config_set ('storage-lifetime', 30*60);
    config_set ('storage-enc', $DOCUMENT_ROOT.'/storage/enc');
    config_set ('http-storage-enc', config_get ('http-document-root').'/storage/enc');

    config_set ('browscap-cache-dir', config_get ('site-root').config_get ('document-root').config_get ('storage-root').'/cache');

    config_set ('content-language', 'ru');
    config_set ('meta-url',         config_get ('proto').'://'.$_SERVER['HTTP_HOST'].$tmp.'/');
    config_set ('meta-keywords',    'school9 sch9 gateway gate tester webtester');
    config_set ('meta-description', 'OnLine testing system');

    config_set ('site-name', 'GATE');

    // Timeout for aproving authorization
    config_set ('confirm_authorize_timeout', 3*24*60*60);

    config_set ('bot-email', 'noreply@school9.perm.ru');
    config_set ('null-email', 'noreply@localhost');

    config_set ('time-zone', '0500');

    config_set ('default-scripts', array ('content.js', 'ipc.js'));

    config_set ('static-privacy-rules', array ('admin'=> array ('/tester/admin.php'=>'ROOT')));

    // How long user can live in system without any activity
    config_set ('user-lifetime', 60*60*24*365);

    config_set ('restore-timeout', 12*60*60*0);

    config_set ('session-lifetime', 15);
  }
?>
