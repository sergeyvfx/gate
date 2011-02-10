<?php
/**
 * Gate - Wiki engine and web-interface for WebTester Server
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

global $id, $page;
formo('title=Редактирование команды;');

$team = team_get_by_id($id);
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    //TODO some check
    frm.submit ();
  }
</script>

<form action=".?action=save&id=<?= $id; ?>&<?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST" onsubmit="check (this); return false;">
  Класс:
  <input type="text" id="grade" name="grade" value="<?= htmlspecialchars($team['grade']); ?>" class="txt block"><div id="hr"></div>
  Полное имя учителя:
  <input type="text" id="teacher_full_name" name="teacher_full_name" value="<?= htmlspecialchars($team['teacher_full_name']); ?>" class="txt block"><div id="hr"></div>
  Полное имя 1-го участника:
  <input type="text" id="pupil1_full_name" name="pupil1_full_name" value="<?= htmlspecialchars($team['pupil1_full_name']); ?>" class="txt block"><div id="hr"></div>
  Полное имя 2-го участника:
  <input type="text" id="pupil2_full_name" name="pupil2_full_name" value="<?= htmlspecialchars($team['pupil2_full_name']); ?>" class="txt block"><div id="hr"></div>
  Полное имя 3-го участника:
  <input type="text" id="pupil3_full_name" name="pupil3_full_name" value="<?= htmlspecialchars($team['pupil3_full_name']); ?>" class="txt block"><div id="hr"></div>
  Платеж:
  <select id="payment_id" name="payment_id" class="block">
    <option value="-1"></option>
    <?php
    $payments = payment_list(user_id());
    foreach ($payments as $p) {
      $amount = $p['amount'];
      if (!preg_match('/\./', $amount)) {
        $amount = $amount . '.00';
      }
      $amount = $amount . ' руб.';
    ?>
      <option value="<?= $p['id'] ?>"><?= $p['date'] . ' ' . $p['cheque_number'] . ' ' . $amount ?></option>
    <?php
    }
    ?>
  </select><div id="hr"></div>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?<?= (($page != '') ? ('&page=' . $page) : ('')); ?>');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
    formc ();
?>
