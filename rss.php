<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * RSS feeder
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  include 'globals.php';
  include $DOCUMENT_ROOT.'/inc/include.php';

  db_connect (config_get ('check-database'));
  content_initialize ();
  wiki_initialize ();
  manage_initialize ();
  security_initialize ();
  ipc_initialize ();
  service_initialize ();
  editor_initialize ();

  $c = service_by_classname ('CSCRSS');

  if (count ($c) <= 0) {
    /* Service not found */
    die;
  }

  /* Get service */
  /* Assume needed service is firct in list */
  $c = $c[0];
  $s = $c->GetService ();

  header ('content-type: application/xhtml+xml');
  print ('<?xml version="1.0" encoding="utf-8"?>');
?>

<rss version="2.0">
  <channel>
    <link><?=$s->GetURL ();?></link>
    <language>ru</language>
    <title><?=$s->GetTitle ();?></title>
    <description><?=$s->GetDescription ();?></description>
    <pubDate><?=FullLocalTime (time ());?></pubDate>
  </channel>
<?=$s->GetRSSData (); ?>
</rss>
