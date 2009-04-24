<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Page generation script for publication
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF != '') {
    print ('HACKERS?');
    die;
  }

  global $self;
  $content = content_lookup (dirname ($self));
  $subnav = '';

  if ($content != null) {
    if ($content->GetAllowed ('READ')) {
      $pIFACE = $content->GetData ();
      $content->Editor_DrawContent (array ('subnav'=>$subnav));
    } else {
      print ('${information}');
      add_info ('Извините, но просмотр содержимого этого разделя для Вас запрещен.');
    }
  } else {
    print ('${information}');
    add_info ('Невозмонжно отобразить страницу, так как запрашиваемый раздел поврежден или не существует. Извините.');
  }
?>
