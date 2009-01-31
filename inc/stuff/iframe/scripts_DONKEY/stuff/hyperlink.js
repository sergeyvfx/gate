var iframeEditor_hyperlinks = new Array ();

function iframeEditor_hyperlinkDialogInit (name) {
  var selection=iframeEditor_GetSelection (name);
  var range=iframeEditor_GetRange (name);
  var a=iframeEditor_GetElementAscensor (range.parentElement (), 'A');
  iframeEditor_hyperlinks[name]=a;
  iframeEditor_LockSelection (name);
  if (!a)
    iframeEditor_hyperlinkDialog_free (name); else
  {
    var data=iframeEditor_hyperlinkDialog_dataFromNode (a);
    iframeEditor_hyperlinkDialog_fill (name, data);
  }
}

function iframeEditor_hyperlinkDialog_dataFromNode (node) {
  var href=node.href.replace (/^(http\:\/\/|ftp\:\/\/|https\:\/\/|mailto\:)/i, '').replace (/(\/*)$/i, '');
  var proto=node.href.replace (/^(http\:\/\/|ftp\:\/\/|https\:\/\/|mailto\:).*/i, '$1');
  if (!node.href.match (/^(http\:\/\/|ftp\:\/\/|https\:\/\/|mailto\:)/i)) proto='';
  var title=node.title;
  var target=node.target;
  proto=proto.toLowerCase ();
  if (proto=='' || proto=='http://') proto='HTTP';
  if (proto=='https://') proto='HTTPS';
  if (proto=='ftp://')   proto='FTP';
  if (proto=='mailto:')  proto='MAILTO';
  return { href:href, proto:proto, target:target, title:title }
}

function iframeEditor_hyperlinkDialog_fill (name, data) {
  iframeEditor_SetDialogValue (name, 'hyperlink', 'href',     data.href);
  iframeEditor_SetDialogValue (name, 'hyperlink', 'protocol', data.proto);
  if (data.target=='blank')
    iframeEditor_SetDialogValue (name, 'hyperlink', 'target',   'BLANK'); else
    iframeEditor_SetDialogValue (name, 'hyperlink', 'target',   'CURRENT');
  iframeEditor_SetDialogValue (name, 'hyperlink', 'title',    data.title);
}

function iframeEditor_hyperlinkDialog_free (name) {
  iframeEditor_hyperlinkDialog_fill (name, {href:'', title:'', proto:'HTTP', target:'CURRENT'});
}

function iframeEditor_hyperlinkDialog_getParams (name) {
  return {
    href   : qtrim (iframeEditor_DialogValue (name, 'hyperlink', 'href')),
    proto  : iframeEditor_DialogValue (name, 'hyperlink', 'protocol'),
    target : iframeEditor_DialogValue (name, 'hyperlink', 'target'),
    title  : qtrim (iframeEditor_DialogValue (name, 'hyperlink', 'title'))
  }
}

function iframeEditor_hyperlinkDialog_hrefNode (name, params) {
  var link=iframeEditor_href (params.proto, params.href);
  var href=iframeEditor_CreateElement (name, 'A');
  var inner=qhtrim (iframeEditor_GetSelectionString (name));
  if (inner=='') inner=link;
  href.href=link;
  href.innerHTML=inner;
  if (params.title!='') href.title=params.title;
  return href;
}

function iframeEditor_hyperlinkDialog_createNew (name) {
   var params=iframeEditor_hyperlinkDialog_getParams (name);
   if (!iframeEditor_hrefCheck (params.proto, params.href))
     return false;
   var selection=iframeEditor_GetSelection (name);
   var range=iframeEditor_GetRange (name);
   var container=range.parentElement ();
   if (container.tagName) {
    var tag=container.tagName.toLowerCase ();
    if (tag=='body' || tag=='div' || tag=='span' || tag=='font' || tag=='p') {
      iframeEditor_InsertNodeAtSelection (name, iframeEditor_hyperlinkDialog_hrefNode (name, params));
      return true;
    }
    return true;
   }
   var link=iframeEditor_href (params.proto, params.href);
   if (container.nodeType==1) { // control
     var acode='<a href="'+link+'"';
     if (params.title!='') acode+=' title="'+params.title+'"';
     if (params.target=='BLANK') acode+=' target="blank"';
     acode+='>';
     var inner='';
     inner=qhtrim (container.innerHTML);
     if (inner=='') inner=link;
     alert (inner);
     container.innerHTML=acode+inner+'</a>';
   } else {
     iframeEditor_InsertNodeAtSelection (name, iframeEditor_hyperlinkDialog_hrefNode (name, params));
   }
  return true;
}

function iframeEditor_hyperlinkDialog_updateHref (name) {
  var href=iframeEditor_hyperlinks [name];
  if (!href) return true;
  var params=iframeEditor_hyperlinkDialog_getParams (name);
  if (!iframeEditor_hrefCheck (params.proto, params.href))
    return false;
  var link=iframeEditor_href (params.proto, params.href);
  href.href=link;
  if (params.title!='') href.title=params.title;   else href.removeAttribute ('title', 0);
  if (params.target=='BLANK') href.target='blank'; else href.removeAttribute ('target', 0);
  return true;
}

function iframeEditor_hyperlinkDialogCancel (name) {
  iframeEditor_UnlockSelection (name);
}

function iframeEditor_hyperlinkDialogAccept (name) {
  var res=false;
  if (!iframeEditor_hyperlinks[name])
    res=iframeEditor_hyperlinkDialog_createNew (name); else
    res=iframeEditor_hyperlinkDialog_updateHref (name);
  iframeEditor_UnlockSelection (name);
  return res;
}