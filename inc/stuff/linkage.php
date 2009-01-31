<?php if ($_stuff_linkage_included_!='##Linkage_Included') { $_stuff_linkage_included_='##Linkage_Included';

  function linkage ($dirs) {
    global $DOCUMENT_ROOT;
    if (!count ($dirs)) return;
    foreach ($dirs as $path) {
      $dir=opendir ($DOCUMENT_ROOT.$path);
      $arr=array ();
      $subdirs=array ();
      while (($file=readdir ($dir))!=false)
        if ($file!='.' && $file!='..')
          {
            $full=$DOCUMENT_ROOT.$path.'/'.$file;
            if (is_file ($full) && eregi (".*\.php$", $file)) $arr[]=$full;
            if (is_dir ($full)) $subdirs[]=$path.'/'.$file;
          }
      array_multisort ($arr,SORT_ASC,SORT_STRING);
      array_multisort ($subdirs,SORT_ASC,SORT_STRING);
      foreach ($arr as $k) include $k;
      linkage ($subdirs);
    }
  }
}
?>
