function iframeEditor_lblockDialogInit (name) {
  iframeEditor_LockSelection (name);
  iframeEditor_lblockDialog_clear (name);
}

function iframeEditor_lblockDialog_clear (name) {
  getElementById ("iframeEditor_"+name+"_lblockDialog_type").value="lblock.blue";
  getElementById ("iframeEditor_"+name+"_lblockDialog_title").value="";
}

function iframeEditor_lblockDialogCancel (name) {  }

function iframeEditor_lblockDialog_insertLBLOCK (name, __type, __title) {
  var tclass=__type.replace (/^lblock\.(.*)/, 't$1');
  var txt=iframeEditor_GetSelectionString (name);

  if (txt=='')     txt="&nbsp;";  

  var node    = iframeEditor_CreateElement (name, 'div');
  var title   = iframeEditor_CreateElement (name, 'div');
  var content = iframeEditor_CreateElement (name, 'div');

  node.className    = "lblock";
  title.className   = "title "+tclass;
  content.className = "content";

  title.innerHTML   = __title;
  content.innerHTML = txt;

  node.appendChild (title);
  node.appendChild (content);

  iframeEditor_InsertNodeAtSelection (name, node);
  return 1;
}

function iframeEditor_lblockDialogAccept (name) {
  var type=getElementById ("iframeEditor_"+name+"_lblockDialog_type").value;
  var title=getElementById ("iframeEditor_"+name+"_lblockDialog_title").value;
  if (type.match (/^lblock/gi)) return iframeEditor_lblockDialog_insertLBLOCK (name, type, title);
  return 1;
}
