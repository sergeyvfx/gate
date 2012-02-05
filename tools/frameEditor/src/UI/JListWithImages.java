package UI;

import java.awt.Component;
import java.awt.datatransfer.Transferable;
import javax.swing.Icon;
import javax.swing.JComponent;
import javax.swing.JList;
import javax.swing.ListCellRenderer;
import javax.swing.ListSelectionModel;
import javax.swing.TransferHandler;

public class JListWithImages extends JList
{
  private static final long serialVersionUID = 1L;

  public JListWithImages()
  {
    setCellRenderer(new ImageCellRenderer());
    setTransferHandler(new ReorderHandler());
    setDragEnabled(true);
    setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
  }

  private class ImageCellRenderer implements ListCellRenderer
  {

    @Override
    public Component getListCellRendererComponent(JList list, Object value, int index,
            boolean isSelected, boolean cellHasFocus)
    {
      Component component = (Component) value;
      component.setBackground(isSelected ? list.getSelectionBackground() : list.getBackground());
      return component;
    }
  }

  private class ReorderHandler extends TransferHandler
  {

    private static final long serialVersionUID = 1L;

    @Override
    public int getSourceActions(JComponent c)
    {
      return TransferHandler.MOVE;
    }

    @Override
    protected Transferable createTransferable(JComponent c)
    {
      return new ImageListData(JListWithImages.this, JListWithImages.this.getSelectedValues());
    }

    @Override
    public Icon getVisualRepresentation(Transferable t)
    {
      return super.getVisualRepresentation(t);
    }
  }
}
