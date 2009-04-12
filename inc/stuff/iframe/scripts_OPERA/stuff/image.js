function iframeEditor_imageDialogInit (name) {
  iframeEditor_LockSelection (name);
}

function iframeEditor_imageDialogCancel (name) {
  iframeEditor_UnlockSelection (name);
}

function iframeEditor_imageDialogAccept (name) {
  var value = getElementById ('iframeEditor_'+name+'_dialog_image_image').value;
  var url   = getElementById ('iframeEditor_'+name+'_dialog_image_image_url').value;
  
  if (value=='') {
    alert ('Пожалуйста, укажите изображение.');
    return false;
  }

  var img=iframeEditor_CreateElement (name, 'IMG');
  img.src=http_host + url;

  iframeEditor_InsertNodeAtSelection (name, img);

  iframeEditor_UnlockSelection (name);

  return true;
}

function iframeEditor_getImg (name)  {
  var result=null;
  var selection=iframeEditor_GetSelection (name);
  var selectedRange;
  if (selection.rangeCount > 0) {
    selectedRange=selection.getRangeAt (0);
    // element node
    if (selectedRange.startContainer.nodeType==1) {
      var aControl=selectedRange.startContainer.childNodes [selectedRange.startOffset];
      if (aControl.tagName.toLowerCase()=='img') result=aControl;
    }
  }
  return result;
}

function iframeEditor_action_img_align (name, align) {
  var selection=iframeEditor_GetSelection (name);
  var img=iframeEditor_getImg (name);

  if (!img) {
    alert ('Не выбрано изображение для применения обтекания.');
    return;
  }

  if (img.className.match (align))
    return;

  img.className=img.className.replace ('right', '').replace ('left', '');

  if (align!='none')
    img.className+=' '+align;
  img.className=img.className.replace (/\s+/, ' ');
}
