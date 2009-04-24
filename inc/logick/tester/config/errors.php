<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Errors' descriptions
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

  if ($_WT_config_errors_included_ != '###WT_config_errors_included###') {
    $_WT_config_errors_included_ != '###WT_config_errors_included###';

    global $WT_errors;
  
    $WT_errors=array (
      'CE' => 'Ошибка компиляции',
      'TL' => 'Превышен предел времени',
      'ML' => 'Превышен предел памяти',
      'RE' => 'Ошибка исполнения',
      'PE' => 'Неверный формат',
      'WA' => 'Неверный ответ',
      'OK' => 'Задача зачтена',

      'IGNORED' => 'Решение проигнорировано',
      'CR'      => 'Сбой системы'

      // By Yermak :)
      /*    'OK' => 'Ух ты!',
            'TL' => 'А побыстрее?',
            'ML' => 'Массивов многовато...',
            'PE' => 'А что на выходе-то?',
            'CE' => 'F9 нажимали?',
            'RE' => 'a[-1] := 1 / 0',
            'WA' => 'Упс!'*/
                      );
  }
?>
