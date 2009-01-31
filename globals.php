<?php
  global $DOCUMENT_ROOT;

  $relative='gate';

  $s=$_SERVER['DOCUMENT_ROOT'];

  if (substr ($s, strlen ($s)-strlen ($relative)-1, strlen ($relative))==$relative)
    $relative='';

  $DOCUMENT_ROOT=$_SERVER['DOCUMENT_ROOT'].$relative;

  $DOCUMENT_ROOT=preg_replace ('/\/*$/', '', $DOCUMENT_ROOT);
?>
