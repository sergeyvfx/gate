var iframeActions=new Array ();

function iframeEditor_Init (name) {
  var editor=iframeEditor_get_editor (name);
  iframeEditor_SetActiveDialog (name, 'default');
  iframeEditor_EnterDesign (name);
  iframeEditor_WriteDefaults (name);
  iframeEditor_SetValue (name);
}

function iframeEditor_WriteDefaults (name) {
  var editor=iframeEditor_get_editor (name);
  editor.contentWindow.document.open ();
  editor.contentWindow.document.write ('<html><head><link rel="stylesheet" type="text/css" href="<?=config_get ('http-document-root');?>/styles/content.css"><link rel="stylesheet" type="text/css" href="<?=config_get ('http-document-root');?>/styles/pages.css"></head><body id="content" class="scontent"><p></p></body></html>');
  editor.contentWindow.document.close ();
}

function iframeEditor_Set_Design (name, design) { var val, editor=iframeEditor_get_editor (name); if (design) val="On"; else val="Off"; editor.contentWindow.document.designMode=val; }
function iframeEditor_EnterDesign (name) { return iframeEditor_Set_Design (name, true); }
function iframeEditor_LeaveDesign (name) { return iframeEditor_Set_Design (name, false); }

function iframeEditor_GetInnerHTML (name) { var editor=iframeEditor_get_editor (name); return editor.contentWindow.document.body.innerHTML;}
function iframeEditor_SetInnerHTML (name, html) { var editor=iframeEditor_get_editor (name); editor.contentWindow.document.body.innerHTML=html;}

function iframeEditor_ExecCommand (name, command) {
  var editor=iframeEditor_get_editor (name);
  switch (command) {
  case 'BOLD': editor.contentWindow.document.execCommand ('bold', false, null); break;
  case 'ITALIC': editor.contentWindow.document.execCommand ('italic', false, null); break;
  case 'UNDERLINE': editor.contentWindow.document.execCommand ('underline', false, null); break;
  case 'STRIKETHROUGH': editor.contentWindow.document.execCommand ('strikethrough', false, null); break;

  case 'ALIGNLEFT': editor.contentWindow.document.execCommand ('justifyleft', false, null); break;
  case 'ALIGNRIGHT': editor.contentWindow.document.execCommand ('justifyright', false, null); break;
  case 'ALIGNJUSTIFY': editor.contentWindow.document.execCommand ('justifyfull', false, null); break;
  case 'ALIGNCENTRE': editor.contentWindow.document.execCommand ('justifycenter', false, null); break;

  case 'BULLETEDLIST': editor.contentWindow.document.execCommand ('insertunorderedlist', false, null); break;
  case 'NUMBEREDLIST': editor.contentWindow.document.execCommand ('insertorderedlist', false, null); break;

  case 'SUBSCRIPT': editor.contentWindow.document.execCommand ('subscript', false, null); break;
  case 'SUPERSCRIPT': editor.contentWindow.document.execCommand ('superscript', false, null); break;

  case 'DECREASEINDENT': editor.contentWindow.document.execCommand ('outdent', false, null); break;
  case 'INCREASEINDENT': editor.contentWindow.document.execCommand ('indent', false, null); break;

  case 'DEFORMAT': iframeEditor_Deformat (name); break;

  case 'UNDO': editor.contentWindow.document.execCommand ('undo', false, null); break;
  case 'REDO': editor.contentWindow.document.execCommand ('redo', false, null); break;
  default: break;
  }
}

function iframeEditor_Deformat(name) {
  text=iframeEditor_GetInnerHTML (name);
  text=Deformat (text);
  iframeEditor_SetInnerHTML (name, text);
}

function iframeEditor_HeaderFormation (name, header) {
  var editor=iframeEditor_get_editor (name); editor.contentWindow.document.execCommand ('FormatBlock', false, '<'+header+'>');
}

function iframeEditor_RegisterAction (action, callback) { iframeActions[action]=callback; }

function iframeEditor_ExecAction (name, action, userdata) {
  if (!iframeActions[action]) return;
  iframeActions[action] (name, userdata);
}

function iframeEditor_GetSelection (name) {
  var editor=iframeEditor_get_editor (name);
  if (iframeEditor_SelectionLocked (name))
    return iframeEditor_GetLockedSelection (name);
  return editor.contentWindow.document.selection;
}
function iframeEditor_GetRange (name) {
  var editor=iframeEditor_get_editor (name);
  if (iframeEditor_SelectionLocked (name))
    return iframeEditor_GetLockedRange (name);
  return iframeEditor_GetSelection (name).createRange ();
}

function iframeEditor_GetSelectionType (name) {
  var editor=iframeEditor_get_editor (name);
  if (iframeEditor_SelectionLocked (name))
    return iframeEditor_GetLockedType (name);
  return iframeEditor_GetSelection (name).type.toLowerCase ();
}

function iframeEditor_GetSelectionString (name) {
  var editor=iframeEditor_get_editor (name);
  if (iframeEditor_SelectionLocked (name))
    return iframeEditor_GetLockedSelectionString (name);
  return iframeEditor_GetRange (name).text;
}

function iframeEditor_GetDocument (name) { var editor=iframeEditor_get_editor (name); return editor.contentWindow.document; }

function iframeEditor_InsertNodeAtSelection (name, insertNode) {
  var edt=iframeEditor_get_editor (name);
  var sel=iframeEditor_GetSelection (name);
  var range=iframeEditor_GetRange (name);
  var type=iframeEditor_GetSelectionType (name);
  if (type=='text') {
    range.pasteHTML (insertNode.outerHTML);
  } else
  if (type=='none') {
    range.collapse ();
    range.pasteHTML (insertNode.outerHTML);
  }
}

function iframeEditor_SetFocus (name) { var editor=iframeEditor_get_editor (name); editor.contentWindow.focus ()}

function iframeEditor_GetSelectionParentNode (name, tagName) {
  var sel=iframeEditor_GetSelection (name);
  var range=sel.createRange ();
  var aControl;
  aControl=range.parentElement ();
  while (aControl.tagName.toLowerCase ()!='BODY') {
    if (aControl.tagName.toLowerCase ()==tagName) return aControl;
    aControl=aControl.parentElement;
  }
  return null;
}

<?php
  include 'actions.js'; print "\n";
  include 'stuff/hyperlink.js'; print "\n";
  include 'stuff/html.js'; print "\n";
  include 'stuff/typography.js'; print "\n";
  include 'stuff/lblock.js'; print "\n";
  include 'stuff/image.js'; print "\n";
  include 'stuff/table.js'; print "\n";
  include 'stuff/insert_file.js'; print "\n";
?>