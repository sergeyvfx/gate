<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * WebTester client page generation script
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

  if (!user_authorized ()) {
    redirect ('./login?redirect='.get_redirection ());
  }

  $gw=WT_spawn_new_gateway ();
  $gw->Handle ();
  $gw->Draw ();
?>
