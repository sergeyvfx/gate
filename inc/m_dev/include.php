<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Includer of main datatype classes
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

  if ($_dev_Included_ != '#dev_Included#') {
    $_dev_Included_ = '#dev_Included#';

    include $DOCUMENT_ROOT.'/inc/m_dev/dataset.php';
    include $DOCUMENT_ROOT.'/inc/m_dev/datatype.php';
    include $DOCUMENT_ROOT.'/inc/m_dev/field.php';
    include $DOCUMENT_ROOT.'/inc/m_dev/storage.php';
    include $DOCUMENT_ROOT.'/inc/m_dev/template.php';
  }
?>
