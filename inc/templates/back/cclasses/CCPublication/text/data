<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Page generation script for pyblication entry
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF != '') {
    print 'HACKERS?';
    die;
  }

  global $self, $uid, $oldid, $pIFACE;
  $content = content_lookup (dirname (dirname ($self)));
  $subnav = '';

  if ($content != null) {
    if ($content->GetAllowed ('READ')) {
      $pIFACE = $content->GetData ();

      if ($oldid != '' && $content->GetAllowed ('EDIT')) {
        $data = $pIFACE->GetListRowbyId ($oldid);
        $subnav = '<div class="contentSub">Содержимое статьи на: <b>'.format_ltime ($data['timestamp']).'</b></div>';
      }

      $pIFACE->Editor_DrawItem ($uid, array ('subnav'=>$subnav));
      draw_template ('Статья / Нижний колонтитул');
    } else {
      print ('${information}');
      add_info ('Извините, но просмотр содержимого этого разделя для Вас запрещен.');
    }
  } else {
    print ('${information}');
    add_info ('Невозмонжно отобразить страницу, так как запрашиваемый раздел поврежден или не существует. Извините.');
  }
?>
