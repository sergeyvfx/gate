var iframeActions=new Array ();

function iframeEditor_Init (name) {
  var editor=iframeEditor_get_editor (name);
  iframeEditor_SetActiveDialog (name, 'default');
  iframeEditor_SetDefaultStyles (name);
  iframeEditor_SetInnerHTML (name, '<p><br></p>');
  iframeEditor_SetValue (name);
  iframeEditor_EnterDesign (name);
}

function iframeEditor_AppendStylesheet (name, stylesheet) {
  var editor=iframeEditor_get_editor (name);
  var s_sheet = editor.contentDocument.createElement ('link');
  var head = editor.contentDocument.getElementsByTagName ('HEAD');
  s_sheet.setAttribute ('rel','stylesheet');
  s_sheet.setAttribute ('type','text/css');
  s_sheet.setAttribute ('href','<?=config_get ('http-document-root');?>/styles/'+stylesheet);
  head[0].appendChild (s_sheet);
}

function iframeEditor_SetDefaultStyles (name) {
  var editor=iframeEditor_get_editor (name);
  iframeEditor_AppendStylesheet (name, 'content.css');
  iframeEditor_AppendStylesheet (name, 'pages.css');
  var body = editor.contentDocument.getElementsByTagName('BODY');
  body[0].id='content';
  body[0].className='scontent';
}

function iframeEditor_Set_Design (name, design) { var val, editor=iframeEditor_get_editor (name); if (design) val="On"; else val="Off"; editor.contentDocument.designMode=val; }
function iframeEditor_EnterDesign (name) { return iframeEditor_Set_Design (name, true); }
function iframeEditor_LeaveDesign (name) { return iframeEditor_Set_Design (name, false); }

function iframeEditor_GetInnerHTML (name) { var editor=iframeEditor_get_editor (name); return editor.contentDocument.body.innerHTML;}
function iframeEditor_SetInnerHTML (name, html) { var editor=iframeEditor_get_editor (name); editor.contentDocument.body.innerHTML=html;}


function iframeEditor_ExecCommand (name, command) {
  var editor=iframeEditor_get_editor (name);
  switch (command) {
  case 'BOLD': editor.contentDocument.execCommand ('bold', false, null); break;
  case 'ITALIC': editor.contentDocument.execCommand ('italic', false, null); break;
  case 'UNDERLINE': editor.contentDocument.execCommand ('underline', false, null); break;
  case 'STRIKETHROUGH': editor.contentDocument.execCommand ('strikethrough', false, null); break;

  case 'ALIGNLEFT': editor.contentDocument.execCommand ('justifyleft', false, null); break;
  case 'ALIGNRIGHT': editor.contentDocument.execCommand ('justifyright', false, null); break;
  case 'ALIGNJUSTIFY': editor.contentDocument.execCommand ('justifyfull', false, null); break;
  case 'ALIGNCENTRE': editor.contentDocument.execCommand ('justifycenter', false, null); break;

  case 'BULLETEDLIST': editor.contentDocument.execCommand ('insertunorderedlist', false, null); break;
  case 'NUMBEREDLIST': editor.contentDocument.execCommand ('insertorderedlist', false, null); break;

  case 'SUBSCRIPT': editor.contentDocument.execCommand ('subscript', false, null); break;
  case 'SUPERSCRIPT': editor.contentDocument.execCommand ('superscript', false, null); break;

  case 'DECREASEINDENT': editor.contentDocument.execCommand ('outdent', false, null); break;
  case 'INCREASEINDENT': editor.contentDocument.execCommand ('indent', false, null); break;

  case 'DEFORMAT': iframeEditor_Deformat (name); break;

  case 'UNDO': editor.contentDocument.execCommand ('undo', false, null); break;
  case 'REDO': editor.contentDocument.execCommand ('redo', false, null); break;
  default: break;
  }
}

function iframeEditor_Deformat(name) {
  text=iframeEditor_GetInnerHTML (name);
  text=Deformat (text);
  iframeEditor_SetInnerHTML (name, text);
}

function iframeEditor_HeaderFormation (name, header) { var editor=iframeEditor_get_editor (name); editor.contentDocument.execCommand ('FormatBlock', false, header); }
function iframeEditor_RegisterAction (action, callback) { iframeActions[action]=callback; }
function iframeEditor_ExecAction (name, action, userdata) {
  if (!iframeActions[action]) return;
  iframeActions[action] (name, userdata);
}

function iframeEditor_GetSelection (name) {
  var editor=iframeEditor_get_editor (name);
  if (iframeEditor_SelectionLocked (name))
    return iframeEditor_GetLockedSelection (name);
  return editor.contentWindow.getSelection ();
}

function iframeEditor_GetRange (name) {
  var editor=iframeEditor_get_editor (name);
  if (iframeEditor_SelectionLocked (name))
    return iframeEditor_GetLockedRange (name);
  return iframeEditor_GetSelection (name).getRangeAt (0);
}

function iframeEditor_GetSelectionType (name) {
  var editor=iframeEditor_get_editor (name);
  if (iframeEditor_SelectionLocked (name))
    return iframeEditor_GetLockedType (name);
  return iframeEditor_GetRange (name).startContainer.nodeType;
}

function iframeEditor_GetSelectionString (name) {
  var editor=iframeEditor_get_editor (name);
  if (iframeEditor_SelectionLocked (name))
    return iframeEditor_GetLockedSelectionString (name);
  return iframeEditor_GetSelection (name).toString ();
}

function iframeEditor_GetDocument (name) { var editor=iframeEditor_get_editor (name); return editor.contentDocument; }

function iframeEditor_InsertNodeAtSelection (editor, insertNode) {
  var edt=iframeEditor_get_editor (editor);
  var win=edt.contentWindow;
  // get saved selection
  var  sel=iframeEditor_GetSelection (editor);
  // get the first range of the selection (there's almost always only one range)
  if (sel.rangeCount > 0) {
    //var range=sel.getRangeAt (0);
    var range=iframeEditor_GetRange (editor);
    // deselect everything
    sel.removeAllRanges ();
    // remove content of current selection from document
    range.deleteContents ();
    // get location of current selection
    var container=range.startContainer;
    var pos=range.startOffset;
    // make a new range for the new selection
    range=document.createRange ();
    if (container.nodeType==3 && insertNode.nodeType==3) {
      // if we insert text in a textnode, do optimized insertion
      container.insertData (pos, insertNode.nodeValue);
      // put cursor after inserted text
      range.setEnd (container, pos+insertNode.length);
      range.setStart (container, pos+insertNode.length);
    } else {
      var afterNode;
      if (container.nodeType==3) {
        // when inserting into a textnode we create 2 new textnodes and put the insertNode in between
        var textNode=container;
        container=textNode.parentNode;
        var text=textNode.nodeValue;
        // text before the split
        var textBefore=text.substr(0,pos);
        // text after the split
        var textAfter=text.substr(pos);
        var beforeNode=document.createTextNode (textBefore);
        var afterNode=document.createTextNode (textAfter);
        // insert the 3 new nodes before the old one
        container.insertBefore (afterNode, textNode);
        container.insertBefore (insertNode, afterNode);
        container.insertBefore (beforeNode, insertNode);
        // remove the old node
        container.removeChild (textNode);
      } else {
        // else simply insert the node
        afterNode = container.childNodes[pos];
        container.insertBefore (insertNode, afterNode);
      }
      range.setEnd (afterNode, 0);
      range.setStart (afterNode, 0);
    }
    sel.addRange (range);
    // remove all ranges
    win.getSelection ().removeAllRanges ();
  } else {
    // There is no selection, so just append new nnode to BODY
    edt.contentDocument.body.appendChild (insertNode);
  }
}

function iframeEditor_SetFocus (name) { var editor=iframeEditor_get_editor (name); editor.contentWindow.focus ()}

function iframeEditor_GetSelectionParentNode (name, tagName) {
  var edt=iframeEditor_get_editor (name);
  var selection=edt.contentWindow.getSelection ();
  var selectedRange=selection.getRangeAt (0);
  var aControl;
  if (selection.rangeCount > 0) {
    selectedRange=selection.getRangeAt (0);
    aControl=selectedRange.startContainer;
    if (aControl.nodeType!=1) aControl=aControl.parentNode;
    while ((aControl.tagName.toLowerCase ()!=tagName) && (aControl.tagName.toLowerCase()!='body'))
      aControl=aControl.parentNode;
  }
  if (aControl.tagName.toLowerCase ()==tagName) return aControl; else return null;
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
