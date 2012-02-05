package UI;

import java.awt.datatransfer.DataFlavor;
import java.awt.datatransfer.Transferable;
import java.awt.datatransfer.UnsupportedFlavorException;
import java.io.IOException;

public class ImageListData implements Transferable
{

  public static final DataFlavor ImageList_Flavor = new DataFlavor(ImageListData.class, "ImageListData");
  private Object[] data;
  private JListWithImages parent;
  private DataFlavor[] supportedFlavors = {ImageList_Flavor};

  protected ImageListData(JListWithImages p, Object[] d)
  {
    parent = p;
    data = d;
  }

  @Override
  public Object getTransferData(DataFlavor flavor)
          throws UnsupportedFlavorException, IOException
  {
    if (flavor.equals(ImageList_Flavor))
    {
      return ImageListData.this;
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
    for (int i = 0; i < supportedFlavors.length; i++)
    {
      if (supportedFlavors[i].match(flavor))
      {
        return true;
      }
    }
    return false;
  }

  public Object[] getData()
  {
    return data;
  }

  public JListWithImages getParent()
  {
    return parent;
  }
}
