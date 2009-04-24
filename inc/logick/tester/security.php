<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Security stuff for  Web-Interface
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

  if ($WT_security_Included != '##WT_security_Included##') {
    $WT_security_Included = '##WT_security_Included##';

    class CGWSecurityInformation extends CSecurityInformation {
      var $security_limits = array (
        'ALL'               => 'Все действия',

        '{{{{GROUP0}}}}'    => 'Контесты',
        'CONTEST.MANAGE'    => 'Управление контестом',
        'CONTEST.CREATE'    => 'Создание контестов', 
        'CONTEST.DELETE'    => 'Удаление контестов',

        '{{{{GROUP1}}}}'    => 'Задачи',
        'PROBLEMS.MANAGE'   => 'Управление задачами',
        'PROBLEMS.CREATE'   => 'Создание задач',
        'PROBLEMS.DELETE'   => 'Удаление задач',
        'PROBLEMS.EDIT'     => 'Редактирование задач',
        'PROBLEMS.REJUDGE'  => 'Перетестирование задач',

        '{{{{GROUP2}}}}'    => 'Чекеры',
        'CHECKERS.MANAGE'   => 'Управление чекерами',
        'CHECKERS.CREATE'   => 'Создание чекеров',
        'CHECKERS.EDIT'     => 'Редактирование чекеров',
        'CHECKERS.DELETE'   => 'Удаление чекеров',

        '{{{{GROUP3}}}}'    => 'Решения участников',
        'SOLUTIONS.MANAGE'  => 'Управление решениями участников',
        'SOLUTIONS.DELETE'  => 'Удаление решений участников',

        '{{{{GROUP4}}}}'      => 'Мониторы',
        'MONITOR.MEGAMONITOR' => 'Построение общих мониторов'
       );

      function CGWSecurityInformation () {
        $this->SetClassName ('CGWSecurityInformation');
      }

      function Init ($name = '', $data = null) {
        CSecurityInformation::Init ($name. $data);
        $this->SetCanInherit (false);
      }
    }
  }
?>
