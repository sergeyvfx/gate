function iframeEditor_tableDialogInit (name) {
  iframeEditor_LockSelection (name);
  iframeEditor_tableDialogFree (name);
}

function iframeEditor_tableDialogCancel (name) {
  iframeEditor_UnlockSelection (name);
}

function iframeEditor_GetTable (editor) {return (iframeEditor_GetSelectionParentNode (editor, 'table'));}
function iframeEditor_GetTableBody (editor) {return (iframeEditor_GetSelectionParentNode (editor, 'tbody'));}
function iframeEditor_GetTableRow (editor) {return (iframeEditor_GetSelectionParentNode (editor, 'tr'));}
function iframeEditor_GetTableCell (editor) {
  var res = iframeEditor_GetSelectionParentNode (editor, 'td');

  if (res) {
    return res;
  }

  return iframeEditor_GetSelectionParentNode (editor, 'th');
}

function iframeEditor_UpdateTRClasses (table) {
  var counter=0;
  var tBody=table.getElementsByTagName ('TBODY')[0];
  for (var i=0; i<tBody.rows.length; i++) {
    var node=tBody.rows.item (i);
    var child=node.cells.item (0);
    var child1=node.cells.item (1);
    if (child.tagName.toLowerCase ()=='th')
      if (child1 && child1.tagName.toLowerCase ()=='th') continue;
    if (counter%2==1) node.className='d'; else node.className='';
    counter++;
  }
}

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

function iframeEditor_action_table_add_row (name) {
  var cell=iframeEditor_GetTableCell (name);
  var row=iframeEditor_GetTableRow (name);
  var tBody=iframeEditor_GetTableBody (name);
  var table=iframeEditor_GetTable (name);

  if (!table || !row) {
    return;
  }

  var newRow = iframeEditor_CreateElement (name, 'TR');

  for (var i = 0; i < row.cells.length; i++) {
    for (var j = 0; j < row.cells.item (i).colSpan; j++) {
      var newCell = iframeEditor_CreateElement (name, row.cells.item(i).tagName);
        newCell.innerHTML = (row.cells.item (i).tagName == 'TD') ? 'Ячейка' : 'Заголовок';
        newRow.appendChild (newCell);
    }
  }

  row = tBody.rows.item (row.rowIndex + 1);
  if (row) {
    tBody.insertBefore (newRow, row);
  } else {
    tBody.appendChild (newRow);
  }

  iframeEditor_UpdateTRClasses (table);
}

function iframeEditor_action_table_add_column (name) {
  var cell = iframeEditor_GetTableCell (name);
  var row = iframeEditor_GetTableRow (name);
  var tBody = iframeEditor_GetTableBody (name);
  var table = iframeEditor_GetTable (name);
  var index = cell.cellIndex;

  if (!table) {
    return;
  }

  for (var i = 0; i < tBody.rows.length; i++) {
    var cIndex = index, curRow = tBody.rows.item (i);

    if (cIndex >= curRow.cells.length) {
      cIndex=curRow.cells.length-1;
    }

    var newCell = iframeEditor_CreateElement (name, curRow.cells.item(cIndex).tagName);
    newCell.innerHTML = (curRow.cells.item (cIndex).tagName == 'TD') ? 'Ячейка' : 'Заголовок';
    before = curRow.cells.item (cell.cellIndex + 1);
    if (before) {
      curRow.insertBefore (newCell, before);
    } else {
      curRow.appendChild (newCell);
    }
  }

  iframeEditor_UpdateTRClasses (table);
}

function iframeEditor_action_table_delete_row (name) {
  var row = iframeEditor_GetTableRow (name);
  var table = iframeEditor_GetTable (name);
  var tBody = iframeEditor_GetTableBody (name);

  if (!table || !row) {
    return;
  }

  tBody.removeChild (row);
  iframeEditor_UpdateTRClasses (table);
}

function iframeEditor_action_table_delete_column (name) {
  var col=iframeEditor_GetTableCell (name);

  if (col) {
    var row = iframeEditor_GetTableRow (name);
    var tBody=iframeEditor_GetTableBody (name);
    var curRow; var index = col.cellIndex;

    for (var i = 0; i < tBody.rows.length; i++) {
      curRow = tBody.rows.item(i);
      if (curRow.cells.length >= index + 1) {
        curRow.deleteCell (index);
      }
    }
  }
}

iframeEditor_RegisterAction ('table_delete', iframeEditor_action_table_delete);
iframeEditor_RegisterAction ('table_add_row', iframeEditor_action_table_add_row);
iframeEditor_RegisterAction ('table_add_column', iframeEditor_action_table_add_column);
iframeEditor_RegisterAction ('table_delete_row', iframeEditor_action_table_delete_row);
iframeEditor_RegisterAction ('table_delete_column', iframeEditor_action_table_delete_column);
