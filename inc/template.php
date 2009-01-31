<?php if ($_template_included_!='#template_Included#') {$_template_included_='#template_Included#'; 
  $tpl_args=array ();
  $tpl_namespace_depth=-1;
  $tpl_stack=array ('count'=>0, 'data'=>array ());
  function tpl_set_args ($args) { global $tpl_args, $tpl_namespace_depth; $tpl_args[$tpl_namespace_depth]=$args; }
  function tpl_arg      ($name) { global $tpl_args, $tpl_namespace_depth; return $tpl_args[$tpl_namespace_depth][$name]; }
  function tpl_args     ()      { global $tpl_args, $tpl_namespace_depth; return $tpl_args[$tpl_namespace_depth]; }
  function tpl_full_args ()      { global $tpl_args; return $tpl_args; }
  function targ         ($name) { return tpl_arg ($name); }
  function targs        ()      { return tpl_args (); }
  function tpl_eval ($src)      { return eval_code ('<? global $pIFACE, $CORE; ?>'.$src); }
  function tpl_parse ($src, $args) {
    $i=0; $n=strlen ($src);
    $tpl='';
    while ($i<$n) {
      $c=$src[$i];
      if ($c=='\\') {
        // Backslahed character
        $i++; $c=$src[$i];
        if ($c=='n') $tpl.="\n"; else
        if ($c=='r') $tpl.="\r"; else
        if ($c=='t') $tpl.="\t"; else $tpl.=$c;
      } else
      if ($c=='<'&&$src[$i+1]=='?') {
        // Some script code
        $scriptCode='';
        while ($i<$n) {
          $ch=$src[$i];
          if ($ch=='>'&&$src[$i-1]=='?') break;
          $scriptCode.=$ch;
          $i++;
        }
        if ($ch=='>') $scriptCode.=$ch;
        $tpl.=$scriptCode;
      } else
      if ($c=='$'&&$src[$i+1]=='{') {
        // Variable
        $token='';
        while ($i<$n) {
          $ch=$src[$i];
          if ($ch=='}') break;
          $token.=$ch;
          $i++;
        }
        if ($ch=='}') {
          $token.=$ch;
          $varName=ereg_replace ('\$\{(.+)\}', '\\1', $token);
          $tpl.=$args[$varName];
        } else $tpl.=$token;
      } else $tpl.=$c;
      $i++;
    }
    return $tpl;
  }
  function tpl_src ($src, $vars=array (),$parse=true, $eval=true) {
    global $tpl_namespace_depth, $tpl_args;
    $tpl_namespace_depth++;
    tpl_set_args ($vars);
    if ($parse) $src=tpl_parse ($src, $vars);
    if ($eval) $r=tpl_eval ($src); else $r=$src;
    $tpl_namespace_depth--;
    return $r;
  }
  function tpl_src_unparsed ($src, $vars=array ()) { return tpl_src ($src, $vars, false, false); }
  function tpl ($name, $vars=array (), $parse=true, $eval=true) {
    global $DOCUMENT_ROOT, $tpl_stack;
    if ($name[0]=='.') {
      $dir=$tpl_stack[$tpl_stack['count']-1];
      $name=preg_replace ('/^\./', '', $name);
      $name=$dir.$name;
    }
    $tpl_stack[$tpl_stack['count']]=dirname ($name);
    $tpl_stack['count']++;
    $src=get_file ($DOCUMENT_ROOT.'/inc/templates/'.$name);
    $res=tpl_src ($src, $vars, $parse, $eval);
    $tpl_stack['count']--;
    return $res;
  }
  function tpl_unparsed ($src, $vars=array ()) { return tpl ($src, $vars, false, false); }
  function tpl_dir_relative () { return '/inc/templates'; }
  function tpl_dir ()          { global $DOCUMENT_ROOT; return $DOCUMENT_ROOT.tpl_dir_relative (); }
  function tplp ($name, $vars=array ()) {print (tpl ($name, $vars));}
  function tpl_srcp ($src, $vars=array ()) {print (tpl_src ($src, $vars));}

  function tpl_linkage ($dir, $vars=array ()) {
    global $DOCUMENT_ROOT, $tpl_stack;
    if ($dir[0]=='.') {
      $_dir=$tpl_stack[$tpl_stack['count']-1];
      $dir=preg_replace ('/^\./', '', $dir);
      $dir=$_dir.$dir;
    }
    $relative=$dir;
    $dir='/inc/templates/'.$dir;
    $arr=dir_listing ($dir);
    for ($i=0; $i<count ($arr); $i++)
      tplp ($relative.'/'.$arr[$i], $vars);
  }
}
?>
