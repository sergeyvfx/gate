package UI.Forms.Domen;

import logic.product.Domen;
import logic.frames.Frameset;
import java.util.ArrayList;
import javax.swing.table.AbstractTableModel;

public class DomenTableModel extends AbstractTableModel
{

  public DomenTableModel()
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
    if (Frameset.getInstance() == null)
    {
      return 0;
    }
    return Frameset.getInstance().getDomens().size();
  }

  @Override
  public Object getValueAt(int rowIndex, int columnIndex)
  {
    return Frameset.getInstance().getDomens().get(rowIndex);
  }

  @Override
  public void setValueAt(Object aValue, int rowIndex, int columnIndex)
  {
    Domen domen = Frameset.getInstance().getDomens().get(rowIndex);
    domen.setName(aValue.toString());
  }

  @Override
  public boolean isCellEditable(int rowIndex, int columnIndex)
  {
    return false;
  }

  public void insertRow(int row, Domen domen)
  {
    Frameset.getInstance().insertDomen(row, domen);
    fireTableRowsInserted(row, row);
  }

  public void removeRow(int row)
  {
    Frameset.getInstance().removeDomen(row);
    fireTableRowsDeleted(row, row);
  }

  public ArrayList<Domen> getDataList()
  {
    return Frameset.getInstance().getDomens();
  }
}
