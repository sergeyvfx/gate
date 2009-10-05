<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Image editing
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $_FILES, $storage, $size, $hlimit, $vlimit, $field,
    $user_id, $formname, $value;

  include '../../../globals.php';
  include $DOCUMENT_ROOT.'/inc/include.php';

  db_connect ();
?>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="<?=config_get ('document-root');?>/styles/content.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script language="JavaScript" src="<?=config_get ('document-root');?>/scripts/core.js"></script>
    <script>
      function onImgChange () {
        document.getElementById ('ipc_form').submit ();
      }
    </script>
  </head>
<?php
  if ($action=='delete') {
    $s=manage_spawn_storage ($storage);
    $s->Unlink ($value);
    $value='';
?>
    <script language="JavaScript" tyle="text/javascript">window.parent.CDCImage_OnImageUpload ('<?=$field;?>', '<?=$formname;?>', '', '');</script>
<?php    
  } else
  if (isset ($_FILES['uploading'])) {
    $data = $_FILES['uploading'];
    $err = validate_image ($data, $size, $hlimit, $vlimit);
    if ($err == '') {
      $s = manage_spawn_storage ($storage);
      $fn = $s->Put ($data, $user_id);
      $value = $fn;
      $full = $s->GetFullURL ($fn);
      $p = $s->GetFileParams ($value);
    ?>
    <script language="JavaScript" type="text/javascript">window.parent.CDCImage_OnImageUpload ('<?=$field;?>', '<?=$formname;?>', '<?=$full;?>', '<?=$fn;?>', '<?=$p['height'];?>', '<?=$p['width'];?>', '<?=$p['mime'];?>');</script>
<?php    } else { ?>
    <script language="JavaScript" type="text/javascript">alert ('<?=$err;?>');</script>
<?php } 
  }
?>
  <body id="content" style="background: transparent">
    <form id="ipc_form" method="POST" action="<?=config_get ('http-document-root');?>/inc/stuff/image/image.edit.php?storage=<?=$storage?>&size=<?=urlencode ($size);?>&vlimit=<?=urlencode ($vlimit);?>&hlimit=<?=urlencode ($hlimit);?>&field=<?=$field;?>&field=<?=$field;?>&user_id=<?=$user_id;?>&formname=<?=$formname;?>&value=<?=$value?>" enctype="multipart/form-data">
      <table class="clear"><tr>
        <td><input type="file" onchange=" onImgChange ();" name="uploading" class="block"></td>
        <?php if ($value!='') { ?><td><button style="width: 200px;" type="button" class="alert" onclick="nav ('<?=config_get ('http-document-root');?>/inc/stuff/image/image.edit.php?storage=<?=$storage?>&size=<?=urlencode ($size);?>&vlimit=<?=urlencode ($vlimit);?>&hlimit=<?=urlencode ($hlimit);?>&field=<?=$field;?>&field=<?=$field;?>&user_id=<?=$user_id;?>&formname=<?=$formname;?>&action=delete&value=<?=$value?>');">Удалить с сервера</button></td> <?php } ?>
      </tr></table>
    </form>
  </body>
</html>

