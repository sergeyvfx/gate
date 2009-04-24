<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * 403 page error generator
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

  print (content_error_page (403));
?>
