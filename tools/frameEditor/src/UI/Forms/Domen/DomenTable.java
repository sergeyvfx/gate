package UI.Forms.Domen;

import logic.product.Domen;
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

public class DomenTable extends JTable
{

  public static DataFlavor DomenTable_Flavor = new DataFlavor(DomenTableData.class, "DomenTableData");
  private static DataFlavor[] supportedFlavors =
  {
    DomenTable_Flavor
  };

  public DomenTable()
  {
    super();
    setTransferHandler(new ReorderHandler());
    setDragEnabled(true);
    setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
  }

  public DomenTable(DomenTableModel m)
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
        Domen draggedData = ((DomenTableData) support.getTransferable().getTransferData(DomenTable_Flavor)).data;
        final DomenTable dragTable = ((DomenTableData) support.getTransferable().getTransferData(DomenTable_Flavor)).parent;
        DomenTableModel dragModel = (DomenTableModel) dragTable.getModel();
        DomenTableModel dropModel = (DomenTableModel) DomenTable.this.getModel();

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
            DomenTable.this.clearSelection();
            DomenTable.this.setRowSelectionInterval(indexToSelect, indexToSelect);
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
      int row = DomenTable.this.getSelectedRow();
      Domen data = (Domen) DomenTable.this.getModel().getValueAt(row, 0);
      return new DomenTableData(DomenTable.this, data);
    }

    @Override
    public boolean canImport(TransferSupport support)
    {
      try
      {
        DomenTable dt = ((DomenTableData) support.getTransferable().getTransferData(DomenTable_Flavor)).parent;
        if (!support.isDrop() || !support.isDataFlavorSupported(DomenTable_Flavor) || !dt.equals(DomenTable.this))
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

  private class DomenTableData implements Transferable
  {

    public Domen data;
    public DomenTable parent;

    protected DomenTableData(DomenTable p, Domen d)
    {
      parent = p;
      data = d;
    }

    public Object getTransferData(DataFlavor flavor) throws UnsupportedFlavorException, IOException
    {
      if (flavor.equals(DomenTable_Flavor))
      {
        return DomenTableData.this;
      } else
      {
        return null;
      }
    }

    public DataFlavor[] getTransferDataFlavors()
    {
      return supportedFlavors;
    }

    public boolean isDataFlavorSupported(DataFlavor flavor)
    {
      return true;
    }
  }
}
