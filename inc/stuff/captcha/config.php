<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Configuration for CAPTCHA image
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

  $CAPTCHA_Config = array (
    'width'     => '140',
    'height'    => '70',

    'colors'    => array (
        'border'      => array (170, 170, 170),
        'foregrounds' => array (array (60, 60, 60),
                                array (150, 60, 60),
                                array (60, 150, 60),
                                array (60, 60, 150)),
        'backgrounds' => array (array (250, 250, 250))
      ),

    'alphabet'         => '0123456789abcdefjhigklmnopqrstuvwxyz',
    'allowed_symbols'  => '0123456789abcdefjhigklmnopqrstuvwxyz',

    'fluctuation_amplitude' => 5,

    'no_spaces'        => false,
    'keystring_length' => rand (5,6),

    'signature' => array (
      'text'       => $_SERVER['HTTP_HOST'],
      'background' => array (204,  204, 255),
      'foreground' => array (96, 96, 96)
    )
  );

?>
