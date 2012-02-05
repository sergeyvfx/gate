package UI.Forms.Rule.Pair;

import logic.product.Pair;
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

public class PairTable extends JTable
{

  public static DataFlavor PairTable_Flavor = new DataFlavor(PairTableData.class, "PairTableData");
  private static DataFlavor[] supportedFlavors =
  {
    PairTable_Flavor
  };

  public PairTable()
  {
    super();
    setTransferHandler(new ReorderHandler());
    setDragEnabled(true);
    setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
  }

  public PairTable(PairTableModel m)
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
        Pair draggedData = ((PairTableData) support.getTransferable().getTransferData(PairTable_Flavor)).data;
        final PairTable dragTable = ((PairTableData) support.getTransferable().getTransferData(PairTable_Flavor)).parent;
        PairTableModel dragModel = (PairTableModel) dragTable.getModel();
        PairTableModel dropModel = (PairTableModel) PairTable.this.getModel();

        if (dropIndex == dropModel.getDataList().size())
        {
          dropIndex--;
          insertionAdjustment++;
        }

        final Object leadItem = dropIndex >= 0 ? dropModel.getDataList().get(dropIndex) : null;

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
            PairTable.this.clearSelection();
            PairTable.this.setRowSelectionInterval(indexToSelect, indexToSelect);
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
      int row = PairTable.this.getSelectedRow();
      Pair data = ((PairTableModel) PairTable.this.getModel()).getValueAt(row);
      return new PairTableData(PairTable.this, data);
    }

    @Override
    public boolean canImport(TransferSupport support)
    {
      try
      {
        PairTable dt = ((PairTableData) support.getTransferable().getTransferData(PairTable_Flavor)).parent;
        if (!support.isDrop() || !support.isDataFlavorSupported(PairTable_Flavor) || !dt.equals(PairTable.this))
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

  private class PairTableData implements Transferable
  {

    public Pair data;
    public PairTable parent;

    protected PairTableData(PairTable p, Pair d)
    {
      parent = p;
      data = d;
    }

    @Override
    public Object getTransferData(DataFlavor flavor) throws UnsupportedFlavorException, IOException
    {
      if (flavor.equals(PairTable_Flavor))
      {
        return PairTableData.this;
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
