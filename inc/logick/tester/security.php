<?php if ($WT_security_Included!='##WT_security_Included##') {$WT_security_Included='##WT_security_Included##';
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
      'SOLUTIONS.DELETE'  => 'Удаление решений участников'
    );

    function CGWSecurityInformation () { $this->SetClassName ('CGWSecurityInformation'); }
    function Init ($name='', $data=null) {
      CSecurityInformation::Init ($name. $data);
      $this->SetCanInherit (false);
    }
  }
}
?>
