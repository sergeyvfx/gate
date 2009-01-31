function iframeEditor_action_formation (name, formation) {
  iframeEditor_ExecCommand (name, formation);
}

function iframeEditor_action_header (name, header) { iframeEditor_HeaderFormation (name, header); }

function iframeEditor_action_full_change (name, userdata) {
  var editor=iframeEditor_get_editor (name);
  var html=getElementById ('iframeEditor_'+name+'_editor_html');
  var elem;
  if (editor.style.display!='none')
    elem=editor; else
    elem=html;
  if (elem.style.height=='')
    elem.style.height=parseInt (elem.clientHeight)*2+'px'; else
    elem.style.height='';
}

function iframeEditor_action_initDialog   (name, dialog) { return eval ('iframeEditor_'+dialog+'DialogInit ("'+name+'");'); }
function iframeEditor_action_cancelDialog (name, dialog) { return eval ('iframeEditor_'+dialog+'DialogCancel ("'+name+'");'); }
function iframeEditor_action_acceptDialog (name, dialog) { return eval ('iframeEditor_'+dialog+'DialogAccept ("'+name+'");'); }

function iframeEditor_action_show_dialog (name, dialog) { iframeEditor_action_initDialog (name, dialog); iframeEditor_SetActiveDialog (name, dialog); }
function iframeEditor_action_dialogCancel (name, dialog) { iframeEditor_action_cancelDialog (name, dialog); iframeEditor_SetActiveDialog (name, 'default'); }
function iframeEditor_action_dialogAccept (name, dialog) { if (iframeEditor_action_acceptDialog (name, dialog)) { iframeEditor_SetActiveDialog (name, 'default'); iframeEditor_SetFocus (name); } }

function iframeEditor_action_insert (name, elem) {
  if (elem=='HR') {
    var node=iframeEditor_CreateElement (name, 'div');
    node.id="hr";
    node.innerHTML='&nbsp;';
    iframeEditor_InsertNodeAtSelection (name, node);
  }
}

function iframeEditor_action_preview (name, userdata) {
  var params='scrollbars=1,fullscreen=0,status=0,toolbar=0,width=740px,height=390px,resizable=1';
  var window=open ('', '', params);
  var s_sheet = window.document.createElement("link");
  s_sheet.setAttribute ('rel','stylesheet'); s_sheet.setAttribute ('type','text/css'); s_sheet.setAttribute ('href','<?=config_get ('http-document-root');?>/styles/content.css');
  var s_sheet2 = window.document.createElement("link");
  s_sheet2.setAttribute ('rel','stylesheet'); s_sheet2.setAttribute ('type','text/css'); s_sheet2.setAttribute ('href','<?=config_get ('http-document-root');?>/styles/content.css');
  var head=window.document.getElementsByTagName('HEAD');
  head[0].appendChild (s_sheet);
  head[0].appendChild (s_sheet2);
  var body=window.document.getElementsByTagName('BODY');
  body[0].id='content';
  body[0].className='scontent';
  window.document.body.innerHTML=iframeEditor_GetInnerHTML (name);
}

iframeEditor_RegisterAction ('formation',     iframeEditor_action_formation);
iframeEditor_RegisterAction ('header',        iframeEditor_action_header);
iframeEditor_RegisterAction ('full_change',   iframeEditor_action_full_change);
iframeEditor_RegisterAction ('show_dialog',   iframeEditor_action_show_dialog);
iframeEditor_RegisterAction ('dialog_cancel', iframeEditor_action_dialogCancel);
iframeEditor_RegisterAction ('dialog_accept', iframeEditor_action_dialogAccept); 
iframeEditor_RegisterAction ('insert',        iframeEditor_action_insert);
iframeEditor_RegisterAction ('preview',       iframeEditor_action_preview);
iframeEditor_RegisterAction ('typography',    iframeEditor_action_typography);
iframeEditor_RegisterAction ('img_align',     iframeEditor_action_img_align);
