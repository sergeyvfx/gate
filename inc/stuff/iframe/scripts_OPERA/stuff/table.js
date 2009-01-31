function iframeEditor_tableDialogInit (name) {
  iframeEditor_LockSelection (name);
  iframeEditor_tableDialogFree (name);
}

function iframeEditor_tableDialogCancel (name) {
  iframeEditor_UnlockSelection (name);
}

function iframeEditor_GetTable (editor) {return (iframeEditor_GetSelectionParentNode (editor, 'table'));}

function iframeEditor_tableDialogFree (name) {
  getElementById ('iframeEditor_'+name+'_tableDialog_title').value='';
  getElementById ('iframeEditor_'+name+'_tableDialog_rowCount').value=5;
  getElementById ('iframeEditor_'+name+'_tableDialog_columnCount').value=5;
  getElementById ('iframeEditor_'+name+'_tableDialog_rowHeaders').checked=true;
  getElementById ('iframeEditor_'+name+'_tableDialog_columnHeaders').checked=true;
}

function iframeEditor_tableDialogAccept (name) {
  var table=iframeEditor_CreateElement (name, 'TABLE');
  var nRows=document.getElementById ('iframeEditor_'+name+'_tableDialog_rowCount').value;
  var nCols=document.getElementById ('iframeEditor_'+name+'_tableDialog_columnCount').value;
  var title=document.getElementById ('iframeEditor_'+name+'_tableDialog_title').value;
  var rowsHeaders=document.getElementById ('iframeEditor_'+name+'_tableDialog_rowHeaders').checked;
  var colsHeaders=document.getElementById ('iframeEditor_'+name+'_tableDialog_columnHeaders').checked;
  nRows=atoi (nRows); nCols=atoi (nCols);
  if (nRows<=0 || nCols<=0) {alert ('Указаны недопустимые значения количества ячеек таблицы.'); return false;}
  // Creating table
  var newTable=iframeEditor_CreateElement (name, 'TABLE');
  var tBody=iframeEditor_CreateElement (name, 'TBODY');
  // Table title
  if (qtrim (title)!='') {
    var caption=iframeEditor_CreateElement (name, 'CAPTION');
    caption.innerHTML=title;
    newTable.appendChild (caption);
  }
  if (colsHeaders) {
    var newRow=iframeEditor_CreateElement (name, 'TR');;
    if (rowsHeaders) {
      var newCell=iframeEditor_CreateElement (name, 'TH');
      newCell.innerHTML='Заголовок';
      newRow.appendChild (newCell);
    }
    for (var j=0; j<nCols; j++) {
      var newCell=iframeEditor_CreateElement (name, 'TH');
      newCell.innerHTML='Заголовок';
      newRow.appendChild (newCell);
    }
    tBody.appendChild (newRow);
  }
  for (var i=0; i<nRows; i++) {
    var newRow=iframeEditor_CreateElement (name, 'TR');
    if (i%2==1) newRow.className='d'; else newRow.className='';
    if (rowsHeaders) {
      var newCell=iframeEditor_CreateElement (name, 'TH');
      newCell.innerHTML='Заголовок';
      newRow.appendChild (newCell);
    }
  for (var j=0; j<nCols; j++) {
      var newCell=iframeEditor_CreateElement (name, 'TD');
      newCell.innerHTML='Ячейка';
      newRow.appendChild (newCell);
    }
    tBody.appendChild (newRow);
  }
  newTable.appendChild (tBody);
  newTable.className='data';
  iframeEditor_InsertNodeAtSelection (name, newTable);
  iframeEditor_UnlockSelection (name);
  return true;
}

function iframeEditor_action_table_delete (name) {
  var table=iframeEditor_GetTable (name);
  if (table) table.parentNode.removeChild (table);
}

iframeEditor_RegisterAction ('table_delete', iframeEditor_action_table_delete);
