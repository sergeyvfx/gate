<?php
  if ($PHP_SELF!='') {print ('HACKERS?'); die;}
  formo ('title=Список существующих хранилищ данных;');
?>
  <table class="list smb">
    <tr class="h">
      <th class="n first">№</th><th width="40%">Название</th><th>Путь</th><th width="48" class="last">&nbsp;</th></tr>
<?php
    for ($i=0; $i<count ($list); $i++) {
      $r=$list[$i];
      $d=!$r->RefCount ();
?>
    <tr<?=(($i<count ($list)-1)?(''):(' class="last"'))?>><td class="n"><?=($i+1);?>.</td><td><a href=".?action=edit&id=<?=$r->GetID ();?>"><?=$r->GetName ();?></a>&nbsp;(<?=$r->RefCount ();?>)</td><td><?=$r->GetPath ();?></td><td align="right"><?ibtnav ('edit.gif', '?action=edit&id='.$r->GetID (), 'Редактировать элемент');?><?ibtnav (($d)?('cross.gif'):('cross_d.gif'), ($d)?('?action=delete&id='.$r->GetID ()):(''), 'Удалить элемент', 'Удалить этот элемент?');?></td></tr>
<?php
    }
?>
  </table>
<?php formc (); ?>
