package UI.Forms.Rule;

import logic.product.Rule;
import java.awt.datatransfer.DataFlavor;
import java.awt.datatransfer.Transferable;
import java.awt.datatransfer.UnsupportedFlavorException;
import java.io.IOException;
import javax.swing.DropMode;
import javax.swing.Icon;
import javax.swing.JComponent;
import javax.swing.JTable;
import javax.swing.ListSelectionModel;
import javax.swing.SwingUtilities;
import javax.swing.TransferHandler;

public class RuleTable extends JTable
{

  public static DataFlavor RuleTable_Flavor = new DataFlavor(RuleTableData.class, "RuleTableData");
  private static DataFlavor[] supportedFlavors =
  {
    RuleTable_Flavor
  };

  public RuleTable()
  {
    super();
    setTransferHandler(new ReorderHandler());
    setDragEnabled(true);
    setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
    setDropMode(DropMode.INSERT_ROWS);
    getTableHeader().setReorderingAllowed(false);
  }

  public RuleTable(RuleTableModel m)
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
        Rule draggedData = ((RuleTableData) support.getTransferable().getTransferData(RuleTable_Flavor)).data;
        final RuleTable dragTable = ((RuleTableData) support.getTransferable().getTransferData(RuleTable_Flavor)).parent;
        RuleTableModel dragModel = (RuleTableModel) dragTable.getModel();
        RuleTableModel dropModel = (RuleTableModel) RuleTable.this.getModel();

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
            RuleTable.this.clearSelection();
            RuleTable.this.setRowSelectionInterval(indexToSelect, indexToSelect);
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
      int row = RuleTable.this.getSelectedRow();
      Rule data = ((RuleTableModel) RuleTable.this.getModel()).getValueAt(row);
      return new RuleTableData(RuleTable.this, data);
    }

    @Override
    public boolean canImport(TransferSupport support)
    {
      try
      {
        RuleTable dt = ((RuleTableData) support.getTransferable().getTransferData(RuleTable_Flavor)).parent;
        if (!support.isDrop() || !support.isDataFlavorSupported(RuleTable_Flavor) || !dt.equals(RuleTable.this))
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

  private class RuleTableData implements Transferable
  {

    public Rule data;
    public RuleTable parent;

    protected RuleTableData(RuleTable p, Rule d)
    {
      parent = p;
      data = d;
    }

    @Override
    public Object getTransferData(DataFlavor flavor) throws UnsupportedFlavorException, IOException
    {
      if (flavor.equals(RuleTable_Flavor))
      {
        return RuleTableData.this;
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
