<?php
if ($_image_validator_!='###imageValidator_Included###') {$_image_validator_='###imageValidator_Included###';

  function validate_image_cmpiterator ($val, $key, $field, $type, $dimension) {
    $r=smartcmp ($val, $key);
    $eq=($type==0)?('равна'):('равен');
    if ($r=='COMPILES') return '';
    if ($r=='NOTCOMPILES') return $field.' изображения не '.$eq.' '.parseint ($key).' '.$dimension.'.';
    if ($r=='GREATER')     return $field.' изображения превосходит '.parseint ($key).' '.$dimension.'.';
    if ($r=='LESS')        return $field.' изображения меньше '.parseint ($key).' '.$dimension.'.';
  } 

  function validate_image ($data, $size='', $hlimit='', $vlimit='') {
    $exts=array ('gif', 'jpg', 'jpeg', 'png');
    $fname=$data['name']; $tmpname=$data['tmp_name'];
    $ext=ereg_replace (".*\.", '', $fname);
    $res='';
    // Check the image extension
    $res=0;
    foreach ($exts as $key) $res|=eregi ('^'.$key.'$', $ext);
    if ($res) {
      $image_size=GetImageSize ($tmpname);
      $w=$image_size[0]; $h=$image_size[1];
      $r=validate_image_cmpiterator ($data['size'], $size, 'Размер', 1, 'байт'); if ($r!='') return $r;
      $r=validate_image_cmpiterator ($w, $hlimit, 'Ширина', 0, 'пикселей'); if ($r!='') return $r;
      $r=validate_image_cmpiterator ($h, $vlimit, 'Высота', 0, 'пикселей'); if ($r!='') return $r;
      return '';
    } else return 'Поддерживаются только следующие расширения файлов: gif, jpg, jpeg, png.';
  }
}
?>
