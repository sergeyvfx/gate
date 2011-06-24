package UI.Forms.Rule.Pair;

import logic.product.Pair;
import javax.swing.table.AbstractTableModel;
import java.util.ArrayList;

public class PairTableModel extends AbstractTableModel
{

  private ArrayList<Pair> pairs;

  public PairTableModel(ArrayList<Pair> part)
  {
    this.pairs = part;
  }

  public PairTableModel()
  {
  }

  @Override
  public int getColumnCount()
  {
    return 2;
  }

  @Override
  public int getRowCount()
  {
    if (pairs == null)
    {
      return 0;
    }
    return pairs.size();
  }

  @Override
  public Object getValueAt(int rowIndex, int columnIndex)
  {
    switch (columnIndex)
    {
      case 0:
        return pairs.get(rowIndex).getFrame().getName() + "."
                + pairs.get(rowIndex).getSlot().getName();
      case 1:
        return pairs.get(rowIndex).getValue().getValue();
      default:
        return null;
    }
  }

  public Pair getValueAt(int row)
  {
    return pairs.get(row);
  }

  @Override
  public boolean isCellEditable(int rowIndex, int columnIndex)
  {
    return false;
  }

  public void insertRow(int row, Pair pair)
  {
    pairs.add(row, pair);
    fireTableRowsInserted(row, row);
  }

  public void removeRow(int row)
  {
    pairs.remove(row);
    fireTableRowsDeleted(row, row);
  }

  public ArrayList<Pair> getDataList()
  {
    return pairs;
  }

  public void setDataList(ArrayList<Pair> part)
  {
    this.pairs = part;
    fireTableDataChanged();
  }

  @Override
  public String getColumnName(int column)
  {
    switch (column)
    {
      case 0:
        return "Слот";
      case 1:
        return "Значение";
      default:
        return "";
    }
  }
}
