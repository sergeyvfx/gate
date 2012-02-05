package UI.Forms.Rule;

import logic.product.Rule;
import javax.swing.table.AbstractTableModel;
import java.util.ArrayList;

public class RuleTableModel extends AbstractTableModel
{
  private ArrayList<Rule> rules = new ArrayList<Rule>();

  public RuleTableModel(ArrayList<Rule> rules)
  {
    this.rules = rules;
  }

  public RuleTableModel()
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
    if (rules == null)
    {
      return 0;
    }
    return rules.size();
  }

  @Override
  public Object getValueAt(int rowIndex, int columnIndex)
  {
    switch (columnIndex)
    {
      case 0:
        return rules.get(rowIndex).getName();
      case 1:
        return rules.get(rowIndex).getText();
      default:
        return null;
    }
  }

  public Rule getValueAt(int row)
  {
    return rules.get(row);
  }

  @Override
  public void setValueAt(Object aValue, int rowIndex, int columnIndex)
  {
    Rule rule = rules.get(rowIndex);
    switch (columnIndex)
    {
      case 0:
        rule.setName(aValue.toString());
        break;
      default:
        break;
    }
  }

  @Override
  public boolean isCellEditable(int rowIndex, int columnIndex)
  {
    return false;
  }

  public void insertRow(int row, Rule rule)
  {
    rules.add(row, rule);
    fireTableRowsInserted(row, row);
  }

  public void removeRow(int row)
  {
    rules.remove(row);
    fireTableRowsDeleted(row, row);
  }

  public ArrayList<Rule> getDataList()
  {
    return rules;
  }

  public void setDataList(ArrayList<Rule> rules)
  {
    this.rules = rules;
    fireTableDataChanged();
  }

  @Override
  public String getColumnName(int column)
  {
    switch (column)
    {
      case 0:
        return "Имя правила";
      case 1:
        return "Правило";
      default:
        return "";
    }
  }
}
