function iframeEditor_insertFileDialogInit (name) {
  iframeEditor_LockSelection (name);
  iframeEditor_insertFileDialogFree (name);
}

function iframeEditor_insertFileDialogCancel (name) {
}

function iframeEditor_insertFileDialogFree (name) {
  getElementById ('iframeEditor_' + name + '_insertFileDialog_text').value = '';
  CDCFile_ZerolizeForm ('file', 'iframeEditor_' + name + '_dialog_insertFile');
}

function iframeEditor_insertFileDialogAccept (name) {
  var value = getElementById ('iframeEditor_' + name + '_dialog_insertFile_file').value;
  var url   = getElementById ('iframeEditor_' + name + '_dialog_insertFile_file_url').value;

  if (value == '') {
    alert ('Пожалуйста, укажите файл.');
    return false;
  }

  var file_node = iframeEditor_CreateElement (name, 'DIV');
  var file_link = iframeEditor_CreateElement (name, 'A');
  var file_img  = iframeEditor_CreateElement (name, 'IMG');

  var text = getElementById ('iframeEditor_' + name + '_insertFileDialog_text').value;

  if (qtrim (text) == '')
    file_link.innerHTML = 'Скачать'; else
    file_link.innerHTML = text;
  file_link.href = http_host + url;

  file_img.src = document_root + '/pics/download.gif';

  file_node.className = 'file_pub';
  file_node.appendChild (file_img);
  file_node.appendChild (file_link);

  iframeEditor_InsertNodeAtSelection (name, file_node);
  iframeEditor_UnlockSelection (name);
  return true;
}
