/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package frameEditor;

import Core.ProductionalActionTableCellEditor;
import logic.frames.ISlot;
import java.util.ArrayList;
import javax.swing.DefaultCellEditor;
import javax.swing.JComboBox;
import javax.swing.JTextField;
import javax.swing.table.TableCellEditor;

/**
 *
 * @author nazgul
 */
public class Utils
{
  public static void updateDepgraph()
  {
    _System.getInstance().getEvents().fireEvent("updateDepgraph");
  }

  public static DefaultCellEditor createCbCellRenderer(String[] values)
  {
    JComboBox cb = new JComboBox(values);
    return new DefaultCellEditor(cb);
  }

  public static TableCellEditor createProductionalCellRenderer(ISlot slot)
  {
    JTextField textField = new JTextField();
    return new ProductionalActionTableCellEditor(slot);
  }

  public static String[] arraylist2strings (ArrayList v)
  {
    String[] names = new String[v.size()];
    int i = 0;

    for (Object d : v) {
      names[i++] = d.toString();
    }

    return names;
  }
}
