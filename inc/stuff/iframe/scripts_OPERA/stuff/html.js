function iframeEditor_htmlDialogInit (name) {
  iframeEditor_htmlDialog_show_html (name, true);
  var html=getElementById ('iframeEditor_'+name+'_editor_html');
  html.value=iframeEditor_GetInnerHTML (name);
}

function iframeEditor_htmlDialog_show_html (name, vis) {
  if (vis) {
    hide ('iframeEditor_'+name+'_editor');
    sb ('iframeEditor_'+name+'_editor_html');
  } else {
    sb ('iframeEditor_'+name+'_editor');
    hide ('iframeEditor_'+name+'_editor_html');
  }
}

function iframeEditor_htmlDialogCancel (name) {
  iframeEditor_htmlDialog_show_html (name, false);
}

function iframeEditor_htmlDialogAccept (name) {
  var html=getElementById ('iframeEditor_'+name+'_editor_html');
  iframeEditor_htmlDialog_show_html (name, false);
  iframeEditor_SetInnerHTML (name, html.value);
  return true;
}