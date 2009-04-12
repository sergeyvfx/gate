<?php
if ($_file_validator_!='###fileValidator_Included###') {$_file_validator_='###fileValidator_Included###';

  function validate_file_cmpiterator ($val, $key, $field, $type, $dimension) {
    $r = smartcmp ($val, $key);
    $eq = ($type == 0) ? ('равна') : ('равен');
    if ($r == 'COMPILES')    return '';
    if ($r == 'NOTCOMPILES') return $field.' изображения не '.$eq.' '.parseint ($key).' '.$dimension.'.';
    if ($r == 'GREATER')     return $field.' изображения превосходит '.parseint ($key).' '.$dimension.'.';
    if ($r == 'LESS')        return $field.' изображения меньше '.parseint ($key).' '.$dimension.'.';
  } 

  function validate_file ($data, $size='') {
    $fname = $data['name'];
    $tmpname = $data['tmp_name'];
    $ext = ereg_replace (".*\.", '', $fname);

    $file_size = $data['size'];
    $r = validate_file_cmpiterator ($data['size'], $size, 'Размер', 1, 'байт');
    if ($r != '')
      {
        return $r;
      }

    return '';
  }
}
?>
