<?php
  global $_FILES, $storage, $size, $field, $user_id, $formname, $value;
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
      function onFileChange () {
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
    <script language="JavaScript" type="text/javascript">
        window.parent.CDCFile_OnFileUpload ('<?=$field;?>', '<?=$formname;?>', '', '');
    </script>
<?php
  } else
  if (isset ($_FILES['uploading'])) {
    $data=$_FILES['uploading'];
    include 'file_validator.php';
    $err=validate_file ($data, $size);
    if ($err=='') {
      $s=manage_spawn_storage ($storage);
      $fn=$s->Put ($data, $user_id);
      $value=$fn;
      $full=$s->GetFullURL ($fn);
      $p=$s->GetFileParams ($value);
    ?>
    <script language="JavaScript" type="text/javascript">window.parent.CDCFile_OnFileUpload ('<?=$field;?>', '<?=$formname;?>', '<?=$full;?>', '<?=$fn;?>', '<?=$p['mime'];?>', '<?=$p['size'];?>');</script>
<?php    } else { ?>
    <script language="JavaScript" type="text/javascript">alert ('<?=$err;?>');</script>
<?php } 
  }
?>
  <body id="content" style="background: transparent">
    <form id="ipc_form" method="POST" action="<?=config_get ('http-document-root');?>/inc/stuff/file/file.edit.php?storage=<?=$storage?>&size=<?=urlencode ($size);?>&field=<?=$field;?>&field=<?=$field;?>&user_id=<?=$user_id;?>&formname=<?=$formname;?>&value=<?=$value?>" enctype="multipart/form-data">
      <table class="clear"><tr>
        <td><input type="file" onchange=" onFileChange ();" name="uploading" class="block"></td>
        <?php if ($value!='' && false) { ?><td><button style="width: 200px;" type="button" class="alert" onclick="nav ('<?=config_get ('http-document-root');?>/inc/stuff/file/file.edit.php?storage=<?=$storage?>&size=<?=urlencode ($size);?>&field=<?=$field;?>&field=<?=$field;?>&user_id=<?=$user_id;?>&formname=<?=$formname;?>&action=delete&value=<?=$value?>');">Удалить с сервера</button></td> <?php } ?>
      </tr></table>
    </form>
  </body>
</html>
