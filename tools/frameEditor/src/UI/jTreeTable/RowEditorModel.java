package UI.jTreeTable;


import javax.swing.table.*;
import java.util.*;

public class RowEditorModel
{

  private Hashtable data;

  public RowEditorModel()
  {
    data = new Hashtable();
  }

  private String hashKey(int row, int col)
  {
    return new Integer(row) + "@" + new Integer(col);
  }

  public void addEditorForRow(int row, int col, TableCellEditor e)
  {
    data.put(hashKey(row, col), e);
  }

  public void removeEditorForRow(int row, int col)
  {
    data.remove(hashKey(row, col));
  }

  public void removeAll()
  {
    data.clear();
  }


  public TableCellEditor getEditor(int row, int col)
  {
    TableCellEditor result = (TableCellEditor) data.get(hashKey(row, col));
    
    if (result == null)
    {
      result = (TableCellEditor) data.get(hashKey(row, -1));
    }

    return result;
  }
}
