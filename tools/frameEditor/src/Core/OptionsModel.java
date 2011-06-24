package Core;

import UI.jTreeTable.DynamicTreeTableModel;
import UI.jTreeTable.TreeTableModel;

public class OptionsModel extends DynamicTreeTableModel
{

  private static final String[] columnNames =
  {
    "Свойство", "Значение"
  };

  private static final String[] methodNames =
  {
    "getOption", "getValue"
  };

  private static final String[] setterMethodNames =
  {
    "getOption", "setValue"
  };

  private static final Class[] classes =
  {
    TreeTableModel.class, String.class, String.class,
  };

  public OptionsModel(Options.OptionEntry root)
  {
    super(root, columnNames, methodNames, setterMethodNames, classes);
  }

  @Override
  public boolean isCellEditable(Object node, int column)
  {
    return true;
  }
}

