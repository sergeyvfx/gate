<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Entrypoint to service administration
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  include '../../globals.php';
  include $DOCUMENT_ROOT.'/inc/include.php';

  Main (dirname ($PHP_SELF), false);
?>
