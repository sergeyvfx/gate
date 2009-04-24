<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Page generation script
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

  /* Redirect to needed page if needed */
  $start_root = opt_get ('start_root');
  if ($start_root != '' && $start_root != '/') {
    redirect (config_get ('document-root').$start_root);
  }

  /* Draw page content */
  $tpl = manage_template_by_name ('Статьи / Заглавная страница');
  print ($tpl->GetText ());
?>
