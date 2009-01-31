<?php if ($PHP_SELF!='') { print 'HACKERS?'; die; }
  if (!user_authorized ()) {
    redirect ('./login?redirect='.get_redirection ());
  }

  $gw=WT_spawn_new_gateway ();
  $gw->Handle ();
  $gw->Draw ();
?>
