package UI.Forms.Value;

import logic.product.Domen;
import logic.product.Value;
import javax.swing.table.AbstractTableModel;
import java.util.ArrayList;

public class ValueTableModel extends AbstractTableModel
{

  private Domen domen = null;

  public ValueTableModel(Domen domen)
  {
    this.domen = domen;
  }

  public ValueTableModel()
  {
  }

  @Override
  public int getColumnCount()
  {
    return 1;
  }

  @Override
  public int getRowCount()
  {
    if (domen == null)
    {
      return 0;
    }
    return domen.getValues().size();
  }

  @Override
  public Object getValueAt(int rowIndex, int columnIndex)
  {
    return domen.getValues().get(rowIndex);
  }

  @Override
  public void setValueAt(Object aValue, int rowIndex, int columnIndex)
  {
    Value value = domen.getValues().get(rowIndex);
    value.setValue(aValue.toString());
  }

  @Override
  public boolean isCellEditable(int rowIndex, int columnIndex)
  {
    return false;
  }

  public void insertRow(int row, Value value)
  {
    domen.insertValue(row, value);
    fireTableRowsInserted(row, row);
  }

  public void removeRow(int row)
  {
    domen.removeValue(row);
    fireTableRowsDeleted(row, row);
  }

  public ArrayList<Value> getDataList()
  {
    return domen.getValues();
  }

  public void setDataList(Domen domen)
  {
    this.domen = domen;
    fireTableDataChanged();
  }
}
