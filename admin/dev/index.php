<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Index page for developers' administation section
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  header ('Location: datatype');
?>

<?php
  include '../../globals.php';
  include $DOCUMENT_ROOT.'/inc/include.php';

  Main (dirname ($PHP_SELF), false);
?>
