package UI.jTreeTable;

/*
 * %W% %E%
 *
 * Copyright 1997, 1998 Sun Microsystems, Inc. All Rights Reserved.
 * 
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met:
 * 
 * - Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer. 
 *   
 * - Redistribution in binary form must reproduce the above
 *   copyright notice, this list of conditions and the following
 *   disclaimer in the documentation and/or other materials
 *   provided with the distribution. 
 *   
 * Neither the name of Sun Microsystems, Inc. or the names of
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.  
 * 
 * This software is provided "AS IS," without a warranty of any
 * kind. ALL EXPRESS OR IMPLIED CONDITIONS, REPRESENTATIONS AND
 * WARRANTIES, INCLUDING ANY IMPLIED WARRANTY OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE OR NON-INFRINGEMENT, ARE HEREBY
 * EXCLUDED. SUN AND ITS LICENSORS SHALL NOT BE LIABLE FOR ANY
 * DAMAGES OR LIABILITIES SUFFERED BY LICENSEE AS A RESULT OF OR
 * RELATING TO USE, MODIFICATION OR DISTRIBUTION OF THIS SOFTWARE OR
 * ITS DERIVATIVES. IN NO EVENT WILL SUN OR ITS LICENSORS BE LIABLE 
 * FOR ANY LOST REVENUE, PROFIT OR DATA, OR FOR DIRECT, INDIRECT,   
 * SPECIAL, CONSEQUENTIAL, INCIDENTAL OR PUNITIVE DAMAGES, HOWEVER  
 * CAUSED AND REGARDLESS OF THE THEORY OF LIABILITY, ARISING OUT OF 
 * THE USE OF OR INABILITY TO USE THIS SOFTWARE, EVEN IF SUN HAS 
 * BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
 * 
 * You acknowledge that this software is not designed, licensed or
 * intended for use in the design, construction, operation or
 * maintenance of any nuclear facility.
 */

import java.awt.Dimension;
import java.awt.Component;
import java.awt.Graphics;
import java.util.Enumeration;
import javax.swing.Icon;
import javax.swing.JTable;
import javax.swing.JTree;
import javax.swing.event.TreeExpansionListener;
import javax.swing.event.TreeSelectionListener;
import javax.swing.table.TableCellEditor;
import javax.swing.table.TableCellRenderer;
import javax.swing.tree.DefaultMutableTreeNode;
import javax.swing.tree.DefaultTreeCellRenderer;
import javax.swing.tree.DefaultTreeSelectionModel;
import javax.swing.tree.TreeModel;
import javax.swing.tree.TreeNode;
import javax.swing.tree.TreePath;

/**
 * This example shows how to create a simple JTreeTable component, 
 * by using a JTree as a renderer (and editor) for the cells in a 
 * particular column in the JTable.  
 *
 * @version %I% %G%
 *
 * @author Philip Milne
 * @author Scott Violet
 */
public class JTreeTable extends JTable
{

  protected RowEditorModel rm;
  protected TreeTableCellRenderer tree;

  public void setRootVisible(boolean visible)
  {
    tree.setRootVisible(visible);
  }

  public JTreeTable(TreeTableModel tm, RowEditorModel rm)
  {
    this (tm);
    this.rm = rm;
  }

  public void setRowEditorModel(RowEditorModel rm)
  {
    this.rm = rm;
  }

  public RowEditorModel getRowEditorModel()
  {
    return rm;
  }

  private boolean isNodeCollased(Object node)
  {
    DefaultMutableTreeNode treeNode = (DefaultMutableTreeNode)node;
    TreeTableModel treeTableModel = (TreeTableModel)tree.getModel();
    TreeNode parentNode = treeNode.getParent();
    TreePath rootPath;

    if (parentNode == null)
    {
      rootPath = new TreePath (((DefaultMutableTreeNode)treeTableModel.getRoot()).getPath());
    } else {
      rootPath = new TreePath (((DefaultMutableTreeNode)treeNode.getParent()).getPath());
    }

    TreePath nodePath = new TreePath (treeNode.getPath());
    Enumeration<TreePath> selected = tree.getExpandedDescendants(rootPath);

    while (selected.hasMoreElements())
    {
      TreePath cur = selected.nextElement();
      if (cur.equals(nodePath))
      {
        return false;
      }
    }

    return true;
  }

  private int getRows(Object node)
  {
    int result = 1;
    DefaultMutableTreeNode treeNode = (DefaultMutableTreeNode)node;
    int count = treeNode.getChildCount();

    for (int i = 0; i < count; ++i)
    {
      result += getRows(treeNode.getChildAt(i));
    }

    return result;
  }

  private int getVisibleRows(Object node)
  {
    int result = 1;

    if (!isNodeCollased(node))
    {
      TreeNode treeNode = (TreeNode)node;
      int count = treeNode.getChildCount();

      for (int i = 0; i < count; ++i)
      {
        result += getVisibleRows (treeNode.getChildAt(i));
      }
    }

    return result;
  }

  public int getModelRowByTableRow(int row)
  {
    return getModelRowByTableRow(null, row) - 1;
  }

  public int getModelRowByTableRow(Object parent, int row)
  {
    TreeTableModel treeTableModel = (TreeTableModel)tree.getModel();

    if (parent == null)
    {
      parent = treeTableModel.getRoot();
    }

    int visibleRowIndex = 1;
    int rowsIndex = 0;

    if (treeTableModel.getRoot() == parent && !tree.isRootVisible())
    {
      visibleRowIndex = 0;
    }

    if (row == 0)
    {
      return 1;
    }

    TreeNode parentNode = (TreeNode)parent;
    int count = parentNode.getChildCount();

    for (int i = 0; i < count; ++i)
    {
      TreeNode curNode = parentNode.getChildAt(i);
      int nodeVisibleRows = getVisibleRows(curNode);
      int nodeRows = getRows(curNode);

      if (visibleRowIndex + nodeVisibleRows > row)
      {
        return rowsIndex + getModelRowByTableRow(curNode, row - visibleRowIndex) + 1;
      }

      rowsIndex += nodeRows;
      visibleRowIndex += nodeVisibleRows;
    }

    return 0;
  }

  @Override
  public TableCellEditor getCellEditor(int row, int col)
  {
    row = getModelRowByTableRow (row);

    //TreePath tp = tree.getPath

    TableCellEditor tmpEditor = null;
    if (rm != null)
    {
      tmpEditor = rm.getEditor(row, col);
    }

    if (tmpEditor != null)
    {
      return tmpEditor;
    }

    return super.getCellEditor(row, col);
  }

  public void addTreeSelectionListener(TreeSelectionListener tsl)
  {
    tree.addTreeSelectionListener(tsl);
  }

  public void removeTreeSelectionListener(TreeSelectionListener tsl)
  {
    tree.removeTreeSelectionListener(tsl);
  }

  public void addTreeExpansionListener(TreeExpansionListener tel)
  {
    tree.addTreeExpansionListener(tel);
  }

  public void removeTreeExpansionListener(TreeExpansionListener tel)
  {
    tree.removeTreeExpansionListener(tel);
  }

  public TreeTableCellRenderer getTreeTableCellRenderer()
  {
    return tree;
  }

  public void setModel(TreeTableModel treeTableModel)
  {
    super.setModel(new TreeTableModelAdapter(treeTableModel, tree));
    tree.setModel(treeTableModel);
  }

  public TreeTableModel getTreeModel()
  {
    return (TreeTableModel)tree.getModel();
  }

  public JTreeTable(TreeTableModel treeTableModel)
  {
    super();

    // Create the tree. It will be used as a renderer and editor.
    tree = new TreeTableCellRenderer(treeTableModel);

    // Install a tableModel representing the visible rows in the tree.
    super.setModel(new TreeTableModelAdapter(treeTableModel, tree));

    // Force the JTable and JTree to share their row selection models.
    tree.setSelectionModel(new DefaultTreeSelectionModel()
    {
      // Extend the implementation of the constructor, as if:
	 /* public this() */


      {
        setSelectionModel(listSelectionModel);
      }
    });
    // Make the tree and table row heights the same.
    tree.setRowHeight(getRowHeight());

    // Install the tree editor renderer and editor.
    setDefaultRenderer(TreeTableModel.class, tree);
    setDefaultEditor(TreeTableModel.class, new TreeTableCellEditor());

    setShowGrid(false);
    setIntercellSpacing(new Dimension(0, 0));
  }

  @Override
  public void setRowHeight(int rowHeight)
  {
    super.setRowHeight(rowHeight);
    if (tree != null) {
      tree.setRowHeight(rowHeight);
    }
  }

  /* Workaround for BasicTableUI anomaly. Make sure the UI never tries to
   * paint the editor. The UI currently uses different techniques to
   * paint the renderers and editors and overriding setBounds() below
   * is not the right thing to do for an editor. Returning -1 for the
   * editing row in this case, ensures the editor is never painted.
   */
  @Override
  public int getEditingRow()
  {
    return (getColumnClass(editingColumn) == TreeTableModel.class) ? -1 : editingRow;
  }

  //
  // The renderer used to display the tree nodes, a JTree.
  //
  public class TreeTableCellRenderer extends JTree implements TableCellRenderer
  {

    protected int visibleRow;

    public void setOpenIcon(Icon icon)
    {
      DefaultTreeCellRenderer renderer = (DefaultTreeCellRenderer)this.getCellRenderer();
      renderer.setOpenIcon(icon);
    }

    public void setClosedIcon(Icon icon)
    {
      DefaultTreeCellRenderer renderer = (DefaultTreeCellRenderer)this.getCellRenderer();
      renderer.setClosedIcon(icon);
    }

    public void setLeafIcon(Icon icon)
    {
      DefaultTreeCellRenderer renderer = (DefaultTreeCellRenderer)this.getCellRenderer();
      renderer.setLeafIcon(icon);
    }

    public TreeTableCellRenderer(TreeModel model)
    {
      super(model);
    }

    @Override
    public void setBounds(int x, int y, int w, int h)
    {
      super.setBounds(x, 0, w, JTreeTable.this.getHeight());
    }

    @Override
    public void paint(Graphics g)
    {
      g.translate(0, -visibleRow * getRowHeight());
      super.paint(g);
    }

    public Component getTableCellRendererComponent(JTable table,
            Object value,
            boolean isSelected,
            boolean hasFocus,
            int row, int column)
    {
      if (isSelected)
      {
        setBackground(table.getSelectionBackground());
      } else
      {
        setBackground(table.getBackground());
      }

      visibleRow = row;
      return this;
    }
  }

  //
  // The editor used to interact with tree nodes, a JTree.
  //
  public class TreeTableCellEditor extends AbstractCellEditor implements TableCellEditor
  {

    public Component getTableCellEditorComponent(JTable table, Object value,
            boolean isSelected, int r, int c)
    {
      return tree;
    }
  }
}

