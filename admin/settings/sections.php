<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Sections list generation script
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

  global $section;
  $sectionMenu = new CVCMenu ();
  $sectionMenu->Init ('sectionMenu', 'type=hor;colorized=true;sublevel=1;border=thin;');
  $first = '';
  $secArr = array ();

  for ($i = 0; $i < count ($sections); $i++) {
    $sectionMenu->AppendItem ($sections[$i], '?section='.$sections[$i], $sections[$i]);
    $secArr[$sections[$i]] = true;
    if ($first == '') {
      $first = $sections[$i];
    }
  }

  if ($section == '' || !$secArr[$section]) {
    $section = $first;
  }

  $sectionMenu->SetActive ($section);
  $sectionMenu->Draw ();

  print ('${information}');

  if ($printSection) {
    include 'section.php';
  }
?>
