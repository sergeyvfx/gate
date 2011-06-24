package UI.Forms.Value;

import logic.product.Value;
import java.awt.datatransfer.DataFlavor;
import java.awt.datatransfer.Transferable;
import java.awt.datatransfer.UnsupportedFlavorException;
import java.io.IOException;
import javax.swing.Icon;
import javax.swing.JComponent;
import javax.swing.JTable;
import javax.swing.ListSelectionModel;
import javax.swing.SwingUtilities;
import javax.swing.TransferHandler;

public class ValueTable extends JTable
{

  public static DataFlavor ValueTable_Flavor = new DataFlavor(ValueTableData.class, "ValueTableData");
  private static DataFlavor[] supportedFlavors =
  {
    ValueTable_Flavor
  };

  public ValueTable()
  {
    super();
    setTransferHandler(new ReorderHandler());
    setDragEnabled(true);
    setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
  }

  public ValueTable(ValueTableModel m)
  {
    this();
    setModel(m);
  }

  public void dropComplete()
  {
  }

  private class ReorderHandler extends TransferHandler
  {

    @Override
    @SuppressWarnings(
    {
      "unchecked", "unchecked"
    })
    public boolean importData(TransferSupport support)
    {
      int dropIndex = getDropLocation().getRow();
      int insertionAdjustment = 0;
      try
      {
        Value draggedData = ((ValueTableData) support.getTransferable().getTransferData(ValueTable_Flavor)).data;
        final ValueTable dragTable = ((ValueTableData) support.getTransferable().getTransferData(ValueTable_Flavor)).parent;
        ValueTableModel dragModel = (ValueTableModel) dragTable.getModel();
        ValueTableModel dropModel = (ValueTableModel) ValueTable.this.getModel();

        if (dropIndex == dropModel.getDataList().size())
        {
          dropIndex--;
          insertionAdjustment++;
        }

        final Object leadItem = dropIndex >= 0 ? dropModel.getDataList().get(dropIndex) : null;
        final int dataLength = 1;

        if (leadItem != null)
        {
          if (draggedData.equals(leadItem))
          {
            return false;
          }
        }

        dragModel.removeRow(dragModel.getDataList().indexOf(draggedData));

        int index = 0;
        final int adjustedLeadIndex = dropModel.getDataList().indexOf(leadItem);


        dropModel.insertRow(adjustedLeadIndex + insertionAdjustment, draggedData);
        index = adjustedLeadIndex + insertionAdjustment;


        final int indexToSelect = index;
        SwingUtilities.invokeLater(new Runnable()
        {

          @Override
          public void run()
          {
            ValueTable.this.clearSelection();
            ValueTable.this.setRowSelectionInterval(indexToSelect, indexToSelect);
          }
        });
      } catch (Exception e)
      {
        e.printStackTrace();
      }
      return false;
    }

    @Override
    public int getSourceActions(JComponent c)
    {
      return TransferHandler.MOVE;
    }

    @Override
    @SuppressWarnings("unchecked")
    protected Transferable createTransferable(JComponent c)
    {
      int row = ValueTable.this.getSelectedRow();
      Value data = (Value) ValueTable.this.getModel().getValueAt(row, 0);
      return new ValueTableData(ValueTable.this, data);
    }

    @Override
    public boolean canImport(TransferSupport support)
    {
      try
      {
        ValueTable dt = ((ValueTableData) support.getTransferable().getTransferData(ValueTable_Flavor)).parent;
        if (!support.isDrop() || !support.isDataFlavorSupported(ValueTable_Flavor) || !dt.equals(ValueTable.this))
        {
          return false;
        }
        return true;
      } catch (Exception e)
      {
        return false;
      }
    }

    @Override
    public Icon getVisualRepresentation(Transferable t)
    {
      return super.getVisualRepresentation(t);
    }
  }

  private class ValueTableData implements Transferable
  {

    public Value data;
    public ValueTable parent;

    protected ValueTableData(ValueTable p, Value d)
    {
      parent = p;
      data = d;
    }

    @Override
    public Object getTransferData(DataFlavor flavor) throws UnsupportedFlavorException, IOException
    {
      if (flavor.equals(ValueTable_Flavor))
      {
        return ValueTableData.this;
      } else
      {
        return null;
      }
    }

    @Override
    public DataFlavor[] getTransferDataFlavors()
    {
      return supportedFlavors;
    }

    @Override
    public boolean isDataFlavorSupported(DataFlavor flavor)
    {
      return true;
    }
  }
}
