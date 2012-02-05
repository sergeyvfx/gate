package UI;

import Core.Model;
import Core.Options;
import Core.OptionsModel;
import logic.frames.Frame;
import logic.frames.Frameset;
import logic.frames.Link;
import logic.frames.Slot;
import UI.Forms.Domen.ChangeDomenDialog;
import UI.graph.Graph;
import UI.jTreeTable.JTreeTable;
import UI.jTreeTable.RowEditorModel;
import UI.jTreeTable.TreeTableModel;
import com.thoughtworks.xstream.XStream;
import com.thoughtworks.xstream.io.xml.DomDriver;
import java.awt.Color;
import java.awt.Cursor;
import java.awt.Graphics;
import java.awt.Point;
import java.awt.Rectangle;
import java.awt.event.ActionEvent;
import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.awt.event.MouseWheelEvent;
import java.awt.event.MouseWheelListener;
import java.awt.geom.Point2D;
import java.awt.geom.Rectangle2D;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.ArrayList;
import javax.swing.AbstractAction;
import javax.swing.BorderFactory;
import javax.swing.ImageIcon;
import javax.swing.JComponent;
import javax.swing.JFileChooser;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPopupMenu;
import javax.swing.JScrollPane;
import javax.swing.JTree;
import javax.swing.SwingUtilities;
import javax.swing.border.BevelBorder;
import javax.swing.event.ListSelectionEvent;
import javax.swing.event.ListSelectionListener;
import javax.swing.event.MenuKeyEvent;
import javax.swing.event.MenuKeyListener;
import javax.swing.event.TreeExpansionEvent;
import javax.swing.event.TreeExpansionListener;
import javax.swing.event.TreeModelEvent;
import javax.swing.event.TreeModelListener;
import javax.swing.event.TreeSelectionEvent;
import javax.swing.event.TreeSelectionListener;
import javax.swing.filechooser.FileFilter;
import javax.swing.tree.DefaultMutableTreeNode;
import javax.swing.tree.TreeNode;
import javax.swing.tree.TreePath;
import org.jgraph.JGraph;
import org.jgraph.event.GraphModelEvent;
import org.jgraph.event.GraphModelListener;
import org.jgraph.event.GraphSelectionEvent;
import org.jgraph.event.GraphSelectionListener;
import org.jgraph.graph.BasicMarqueeHandler;
import org.jgraph.graph.CellHandle;
import org.jgraph.graph.CellView;
import org.jgraph.graph.DefaultCellViewFactory;
import org.jgraph.graph.DefaultEdge;
import org.jgraph.graph.DefaultGraphCell;
import org.jgraph.graph.DefaultPort;
import org.jgraph.graph.EdgeView;
import org.jgraph.graph.GraphConstants;
import org.jgraph.graph.GraphContext;
import org.jgraph.graph.Port;
import org.jgraph.graph.PortView;
import frameEditor.FileStorage;
import frameEditor._System;

/**
 *
 * @author nazgul
 */
public class MainForm extends javax.swing.JFrame
{

  private JGraph graph;
  private static double MAX_SCALE = 2.0;
  private static double MIN_SCALE = 0.2;
  private static double STEP_SCALE = 0.1;
  private JFileChooser fileChooser;
  private boolean isChange;
  private File file;
  private JScrollPane graphScrollPane, optionsScrollPane;
  private boolean ignoreSelectionChaged = false;
  private JTreeTable treeTable;
  private Model model;
  private boolean initialized = false;
  private Options treeTableOptions;
  private RowEditorModel treeTableRowEditorModel;
  // <editor-fold defaultstate="collapsed" desc="Listeners">

  public interface OptionsUpdater
  {

    void update();
  }
  public OptionsUpdater optionsUpdater = new OptionsUpdater()
  {

    @Override
    public void update()
    {
      if (supressExpansionEvent || treeTable == null)
      {
        return;
      }

      supressExpansionEvent = true;
      TreeTableModel newModel = createOptionsModel();
      newModel.addTreeModelListener(treeModelListener);
      treeTable.setModel(newModel);
      supressExpansionEvent = false;

      restoreTree();
    }
  };
  private ListSelectionListener treeTableListSelectionListener = new ListSelectionListener()
  {

    @Override
    public void valueChanged(ListSelectionEvent e)
    {
      checkButtons();
    }
  };
  private TreeSelectionListener treeTableSelectionListener = new TreeSelectionListener()
  {

    @Override
    public void valueChanged(TreeSelectionEvent e)
    {
      checkButtons();
    }
  };
  private TreeSelectionListener treeSelectionListener = new TreeSelectionListener()
  {

    @Override
    public void valueChanged(TreeSelectionEvent e)
    {
      if (ignoreSelectionChaged)
      {
        return;
      }

      Object node = jOutlinerTree.getLastSelectedPathComponent();

      if (node == null || node instanceof String)
      {
        return;
      }

      ignoreSelectionChaged = true;
      graph.setSelectionCell((DefaultMutableTreeNode) node);
      ignoreSelectionChaged = false;
    }
  };
  private TreeExpansionListener treeExpansionListener = new TreeExpansionListener()
  {

    @Override
    public void treeExpanded(TreeExpansionEvent event)
    {
      processTreeExpansion(event);
    }

    @Override
    public void treeCollapsed(TreeExpansionEvent event)
    {
      processTreeCollapse(event);
    }
  };
  private MouseListener treeTableMouseListener = new MouseListener()
  {

    @Override
    public void mouseClicked(MouseEvent e)
    {
    }

    @Override
    public void mousePressed(MouseEvent e)
    {
      treeTable.repaint();
    }

    @Override
    public void mouseReleased(MouseEvent e)
    {
    }

    @Override
    public void mouseEntered(MouseEvent e)
    {
    }

    @Override
    public void mouseExited(MouseEvent e)
    {
    }
  };
  private TreeModelListener treeModelListener = new TreeModelListener()
  {

    @Override
    public void treeNodesChanged(TreeModelEvent e)
    {
      graph.updateUI();
      jOutlinerTree.updateUI();
      treeTable.repaint();
      checkButtons();
    }

    @Override
    public void treeNodesInserted(TreeModelEvent e)
    {
    }

    @Override
    public void treeNodesRemoved(TreeModelEvent e)
    {
    }

    @Override
    public void treeStructureChanged(TreeModelEvent e)
    {
    }
  };
  private GraphSelectionListener graphSelectionListener = new GraphSelectionListener()
  {

    @Override
    public void valueChanged(GraphSelectionEvent e)
    {
      graphValueChanged(e);
    }
  };
  private KeyListener keyListener = new KeyListener()
  {

    @Override
    public void keyTyped(KeyEvent e)
    {
    }

    @Override
    public void keyPressed(KeyEvent e)
    {
      graphKeyPressed(e);
    }

    @Override
    public void keyReleased(KeyEvent e)
    {
    }
  };
  private MouseWheelListener mouseWheelListener = new MouseWheelListener()
  {

    @Override
    public void mouseWheelMoved(MouseWheelEvent e)
    {
      graphMouseWheelMoved(e);
    }
  };
  private GraphModelListener graphModelListener = new GraphModelListener()
  {

    @Override
    public void graphChanged(GraphModelEvent e)
    {
      setIsChange(true);

      /*if (e.getChange().getRemoved() != null)
      {
      return;
      }*/

      updateOptionsModel();
      jOutlinerTree.updateUI();

      checkButtons();
    }
  };
  // </editor-fold>

  /** Creates new form MainForm */
  public MainForm()
  {
    initComponents();
    setButtonsEnable(false);
    isChange = false;
    updateTitle();
  }

  public void initAll()
  {
    if (initialized)
    {
      return;
    }

    initGraph();
    initTree();
    initOptionsTable();

    installListener();

    checkButtons();
    initialized = true;

    expandedTreeObjects = new LinkedList<String>();
  }

  public void uninitAll()
  {
    if (!initialized)
    {
      return;
    }

    file = null;

    graph.getParent().remove(graph);
    graphScrollPane.getParent().remove(graphScrollPane);
    graph = null;
    graphScrollPane = null;

    treeTable.getParent().remove(treeTable);
    optionsScrollPane.getParent().remove(optionsScrollPane);
    optionsScrollPane = null;
    treeTable = null;

    initialized = false;

    Frameset.getInstance().clear();
  }

  public void initGraph()
  {
    graph = new Graph(model);
    graph.getGraphLayoutCache().setFactory(new DefaultCellViewFactory()
    {
      // Override Superclass Method to Return Custom EdgeView

      @Override
      protected EdgeView createEdgeView(Object cell)
      {

        // Return Custom EdgeView
        return new EdgeView(cell)
        {

          /**
           * Returns a cell handle for the view.
           */
          @Override
          public CellHandle getHandle(GraphContext context)
          {
            return new EdgeHandle(this, context);
          }
        };
      }
    });

    graph.setMarqueeHandler(new MarqueeHandler());
    graphScrollPane = new JScrollPane(graph);
    jPanelGraph.add(graphScrollPane);
  }

  private void initTree()
  {
    jOutlinerTree.setModel(model);

    jOutlinerTree.addTreeSelectionListener(treeSelectionListener);
  }

  public void updateOptionsModel()
  {
    optionsUpdater.update();
  }

  public void initOptionsTable()
  {
    RowEditorModel rm = new RowEditorModel();
    treeTableRowEditorModel = rm;

    TreeTableModel treeModel = createOptionsModel();

    treeTable = new JTreeTable(treeModel);

    treeTable.setRootVisible(false);
    treeTable.setRowSelectionAllowed(true);

    treeTable.setRowEditorModel(rm);
    treeTable.setRowHeight(20);

    //treeTable.getTreeTableCellRenderer().setOpenIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/tree_collapse.png")));
    //treeTable.getTreeTableCellRenderer().setClosedIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/tree_expand.png")));
    treeTable.updateUI();
    /*treeTable.getTreeTableCellRenderer().setOpenIcon(null);
    treeTable.getTreeTableCellRenderer().setClosedIcon(null);*/
    treeTable.getTreeTableCellRenderer().setLeafIcon(null);

    optionsScrollPane = new JScrollPane(treeTable);
    optionsScrollPane.getViewport().setBackground(treeTable.getBackground());
    jOptionsPanel.add(optionsScrollPane);
  }

  protected TreeTableModel createOptionsModel()
  {
    Options options = new Options(Frameset.getInstance().getActiveFrame(), treeTableRowEditorModel, optionsUpdater);
    treeTableOptions = options;
    TreeTableModel treeTableModel = new OptionsModel(options.getRoot());
    return treeTableModel;
  }

  private void installListener()
  {
    //Add mouse wheel listener for zooming
    graph.addMouseWheelListener(mouseWheelListener);
    graph.getSelectionModel().addGraphSelectionListener(graphSelectionListener);
    graph.addKeyListener(keyListener);
    graph.getModel().addGraphModelListener(graphModelListener);
    treeTable.addMouseListener(treeTableMouseListener);
    treeTable.getTreeModel().addTreeModelListener(treeModelListener);
    treeTable.addTreeExpansionListener(treeExpansionListener);
    treeTable.addTreeSelectionListener(treeTableSelectionListener);
    treeTable.getSelectionModel().addListSelectionListener(treeTableListSelectionListener);
    treeTable.getColumnModel().getSelectionModel().addListSelectionListener(treeTableListSelectionListener);
  }

  private void uninstallListeners()
  {
    graph.removeMouseWheelListener(mouseWheelListener);
    graph.getSelectionModel().removeGraphSelectionListener(graphSelectionListener);
    graph.removeKeyListener(keyListener);
    graph.getModel().removeGraphModelListener(graphModelListener);
    treeTable.getTreeModel().removeTreeModelListener(treeModelListener);
    treeTable.removeMouseListener(treeTableMouseListener);
    treeTable.removeTreeExpansionListener(treeExpansionListener);
    treeTable.removeTreeSelectionListener(treeTableSelectionListener);
    treeTable.getSelectionModel().removeListSelectionListener(treeTableListSelectionListener);
    treeTable.getColumnModel().getSelectionModel().removeListSelectionListener(treeTableListSelectionListener);
  }

  // <editor-fold defaultstate="collapsed" desc="Actions with files">
  private void create()
  {
    model = new Model();

    close();
    initAll();
    setIsChange(true);
    setButtonsEnable(true);
  }

  private void open()
  {
    if (isChange)
    {
      if (askSaveOrNot() == JOptionPane.YES_OPTION)
      {
        save();
      }
    }
    initFileChooser();
    if (fileChooser.showOpenDialog(this) == JFileChooser.APPROVE_OPTION)
    {
      try
      {

        file = fileChooser.getSelectedFile();
        FileInputStream in = new FileInputStream(file);
        byte[] fileData = new byte[in.available()];

        in.read(fileData);
        in.close();

        String xml = new String(fileData);
        XStream xstream = new XStream(new DomDriver());
        FileStorage dummy = (FileStorage) xstream.fromXML(xml);

        Frameset.setInstance(dummy.getFrameset());
        model = (Model) dummy.getModel();
        initAll();
        graph.setModel(model);

        setButtonsEnable(true);
        updateTitle();
      } catch (FileNotFoundException ex)
      {
        showError(ex.getMessage());
      } catch (IOException ex)
      {
        ex.printStackTrace();
      }
    }
  }

  private void saveAs()
  {
    initFileChooser();
    if (fileChooser.showSaveDialog(graph) == JFileChooser.APPROVE_OPTION)
    {
      writeToFile(fileChooser.getSelectedFile());
      setIsChange(false);
    }
  }

  private void export()
  {
    JFileChooser fc = new JFileChooser(new File("./kb").getAbsolutePath());
    FileFilter fileFilter = new FileFilter()
    {

      @Override
      public boolean accept(File f)
      {
        return (f != null)
                && ((f.getName() != null)
                && f.getName().endsWith(".frs") || f.isDirectory());
      }

      @Override
      public String getDescription()
      {
        return "Frameset file (.frs)";
      }
    };
    fc.setFileFilter(fileFilter);
    fc.setSelectedFile(null);
    if (fc.showSaveDialog(graph) == JFileChooser.APPROVE_OPTION)
    {
      try {
        Frameset.getInstance().save(fc.getSelectedFile());
      } catch (IOException ex) {
        ex.printStackTrace(System.err);
      }
    }
  }

  private void save()
  {
    if (file != null)
    {
      writeToFile(file);
      setIsChange(false);
    } else
    {
      saveAs();
    }
  }

  private void close()
  {
    int res = JOptionPane.NO_OPTION;
    if (isChange)
    {
      res = askSaveOrNot();
      if (res == JOptionPane.YES_OPTION)
      {
        save();
      }
    }
    if (!isChange || res == JOptionPane.NO_OPTION)
    {
      uninitAll();
      setIsChange(false);
      setButtonsEnable(false);
    }
  }

  private void exit()
  {
    close();
    if (!initialized)
    {
      this.dispose();
    }
  }
  // </editor-fold>

  // <editor-fold defaultstate="collapsed" desc="Utils">
  private void showError(String msg)
  {
    JOptionPane.showMessageDialog(this, msg, "Ошибка",
            JOptionPane.ERROR_MESSAGE);
  }

  private void writeToFile(File f)
  {
    try
    {
      if (!f.getCanonicalPath().endsWith(".fre"))
      {
        f = new File(f.getCanonicalPath() + ".fre");
      }
      uninstallListeners();
      Writer out = new OutputStreamWriter(new FileOutputStream(f));
      FileStorage dummy = new FileStorage(Frameset.getInstance(), graph.getModel());

      XStream xstream = new XStream(new DomDriver());
      String xml = xstream.toXML(dummy);

      out.write(xml);
      out.flush();
      out.close();

      file = f;
      installListener();
    } catch (FileNotFoundException ex)
    {
      showError(ex.getMessage());
    } catch (IOException ex)
    {
      showError(ex.getMessage());
    }
  }

  private int askSaveOrNot()
  {
    return JOptionPane.showConfirmDialog(this,
            "Файл был изменен. Сохранить изменения?",
            "Сохранение изменений", JOptionPane.YES_NO_OPTION,
            JOptionPane.QUESTION_MESSAGE);
  }

  private void updateTitle()
  {
    String title = "Frame Editor";
    String fileName = "";
    String change = "";
    if (file != null)
    {
      fileName = " - [ " + file.getName() + " ]";
      if (isChange)
      {
        change = "*";
      }
    } else
    {
      if (isChange)
      {
        change = " - [ New.fre ]*";
      }
    }
    title = title + fileName + change;
    setTitle(title);
  }

  public void setIsChange(boolean b)
  {
    isChange = b;
    updateTitle();
    jMenuFileItemSave.setEnabled(b);
    jButtonSave.setEnabled(b);
  }

  private void initFileChooser()
  {
    if (fileChooser == null)
    {
      fileChooser = new JFileChooser(new File("./kb").getAbsolutePath());
      FileFilter fileFilter = new FileFilter()
      {

        @Override
        public boolean accept(File f)
        {
          return (f != null)
                  && ((f.getName() != null)
                  && f.getName().endsWith(".fre") || f.isDirectory());
        }

        @Override
        public String getDescription()
        {
          return "Frame Editor file (.fre)";
        }
      };
      fileChooser.setFileFilter(fileFilter);
      fileChooser.setSelectedFile(null);
    }
  }

  private void setButtonsEnable(boolean b)
  {
    jMainPanel.setVisible(b);

    jMenuFileItemClose.setEnabled(b);
    jMenuFileItemSave.setEnabled(b);
    jMenuFileItemSaveAs.setEnabled(b);
    jMenuEditItemDomens.setEnabled(b);

    jButtonSave.setEnabled(b);

    jButtonAdd.setEnabled(b);
    jButtonRemove.setEnabled(b);
    jButtonZoomIn.setEnabled(b);
    jButtonZoomOut.setEnabled(b);
    jButtonGroup.setEnabled(b);
    jButtonUnGroup.setEnabled(b);
  }
  // </editor-fold>

  private void graphKeyPressed(KeyEvent e)
  {
    switch (e.getKeyCode())
    {
      case KeyEvent.VK_DELETE:
        remove();
        break;
      default:
        break;
    }
  }

  private void graphValueChanged(GraphSelectionEvent gse)
  {
    jButtonGroup.setEnabled(graph.getSelectionCount() > 1);
    boolean enable = !graph.isSelectionEmpty();
    jButtonRemove.setEnabled(enable);
    jButtonUnGroup.setEnabled(enable);

    DefaultGraphCell cell = (DefaultGraphCell) graph.getSelectionCell();
    selectOutlinerObject(cell);

    Frame frame = null;
    if (cell != null)
    {
      Object object = ((DefaultGraphCell) graph.getSelectionCell()).getUserObject();
      if (object instanceof Frame)
      {
        frame = (Frame) object;
      }
    }

    Frameset.getInstance().setActiveFrame(frame);
    updateOptionsModel();
    checkButtons();
  }

  private void graphMouseWheelMoved(MouseWheelEvent e)
  {
    if (e.isControlDown())
    {
      int rotation = e.getWheelRotation() * (-1);
      double oldScale = graph.getScale();
      double newScale = oldScale + (STEP_SCALE * rotation);
      if ((newScale > MIN_SCALE) && (newScale < MAX_SCALE))
      {
        graph.setScale(newScale, e.getPoint());
      }
    }
  }

  private void selectOutlinerObject(DefaultGraphCell ob)
  {
    if (ignoreSelectionChaged)
    {
      return;
    }

    ignoreSelectionChaged = true;
    TreePath treePath = model.getTreePath(ob);
    jOutlinerTree.setSelectionPath(treePath);

    if (treePath.getPathCount() > 1)
    {
      jOutlinerTree.scrollPathToVisible(treePath);
    }

    ignoreSelectionChaged = false;
  }

  /** This method is called from within the constructor to
   * initialize the form.
   * WARNING: Do NOT modify this code. The content of this method is
   * always regenerated by the Form Editor.
   */
  @SuppressWarnings("unchecked")
  // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
  private void initComponents() {

    buttonGroup1 = new javax.swing.ButtonGroup();
    jStatusBar = new javax.swing.JPanel();
    jStatusLabel = new javax.swing.JLabel();
    jMainPanel = new javax.swing.JPanel();
    kbEditor = new javax.swing.JSplitPane();
    jSplitPane2 = new javax.swing.JSplitPane();
    jScrollPane1 = new javax.swing.JScrollPane();
    jOutlinerTree = new javax.swing.JTree();
    jPanel1 = new javax.swing.JPanel();
    jOptionsPanel = new javax.swing.JPanel();
    jPanel3 = new javax.swing.JPanel();
    jBtnInsertSlot = new javax.swing.JButton();
    jBtnEditSlot = new javax.swing.JButton();
    jBtnDeleteSlot = new javax.swing.JButton();
    jPanelGraph = new javax.swing.JPanel();
    jPanelToolBar = new javax.swing.JPanel();
    jToolBarFile = new javax.swing.JToolBar();
    jButtonNew = new javax.swing.JButton();
    jButtonOpen = new javax.swing.JButton();
    jButtonSave = new javax.swing.JButton();
    jSeparator7 = new javax.swing.JToolBar.Separator();
    jToolBarGraph = new javax.swing.JToolBar();
    jButtonAdd = new javax.swing.JButton();
    jButtonRemove = new javax.swing.JButton();
    jSeparator1 = new javax.swing.JToolBar.Separator();
    jButtonZoomIn = new javax.swing.JButton();
    jButtonZoomOut = new javax.swing.JButton();
    jSeparator3 = new javax.swing.JToolBar.Separator();
    jButtonGroup = new javax.swing.JButton();
    jButtonUnGroup = new javax.swing.JButton();
    jMenuBar = new javax.swing.JMenuBar();
    jMenuFile = new javax.swing.JMenu();
    jMenuFileItemNew = new javax.swing.JMenuItem();
    jMenuFileItemOpen = new javax.swing.JMenuItem();
    jMenuFileItemSave = new javax.swing.JMenuItem();
    jMenuFileItemSaveAs = new javax.swing.JMenuItem();
    jMenuFileItemExport = new javax.swing.JMenuItem();
    jMenuFileItemClose = new javax.swing.JMenuItem();
    jSeparator5 = new javax.swing.JPopupMenu.Separator();
    jMenuFileItemExit = new javax.swing.JMenuItem();
    jMenuEdit = new javax.swing.JMenu();
    jMenuEditItemDomens = new javax.swing.JMenuItem();
    jMenuHelp = new javax.swing.JMenu();
    jMenuHelpItemContents = new javax.swing.JMenuItem();
    jSeparator6 = new javax.swing.JPopupMenu.Separator();
    jMenuHelpItemAbout = new javax.swing.JMenuItem();

    setDefaultCloseOperation(javax.swing.WindowConstants.DO_NOTHING_ON_CLOSE);
    setTitle("Frame Editor");
    addWindowListener(new java.awt.event.WindowAdapter() {
      public void windowClosing(java.awt.event.WindowEvent evt) {
        formWindowClosing(evt);
      }
      public void windowOpened(java.awt.event.WindowEvent evt) {
        formWindowOpened(evt);
      }
    });

    jStatusBar.setLayout(new java.awt.GridLayout(1, 1));

    jStatusLabel.setText(" ");
    jStatusBar.add(jStatusLabel);

    getContentPane().add(jStatusBar, java.awt.BorderLayout.SOUTH);

    jMainPanel.setLayout(new java.awt.CardLayout());

    kbEditor.setDividerLocation(250);
    kbEditor.setOneTouchExpandable(true);
    kbEditor.setPreferredSize(new java.awt.Dimension(751, 600));

    jSplitPane2.setDividerLocation(150);
    jSplitPane2.setOrientation(javax.swing.JSplitPane.VERTICAL_SPLIT);

    jScrollPane1.setBorder(null);

    javax.swing.tree.DefaultMutableTreeNode treeNode1 = new javax.swing.tree.DefaultMutableTreeNode("root");
    jOutlinerTree.setModel(new javax.swing.tree.DefaultTreeModel(treeNode1));
    jScrollPane1.setViewportView(jOutlinerTree);

    jSplitPane2.setTopComponent(jScrollPane1);

    jPanel1.setLayout(new java.awt.BorderLayout());

    jOptionsPanel.setLayout(new javax.swing.BoxLayout(jOptionsPanel, javax.swing.BoxLayout.LINE_AXIS));
    jPanel1.add(jOptionsPanel, java.awt.BorderLayout.CENTER);

    jPanel3.setLayout(new java.awt.GridLayout(3, 1, 5, 5));

    jBtnInsertSlot.setText("Вставить");
    jBtnInsertSlot.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jBtnInsertSlotActionPerformed(evt);
      }
    });
    jPanel3.add(jBtnInsertSlot);

    jBtnEditSlot.setText("Редактировать");
    jBtnEditSlot.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jBtnEditSlotActionPerformed(evt);
      }
    });
    jPanel3.add(jBtnEditSlot);

    jBtnDeleteSlot.setText("Удалить");
    jBtnDeleteSlot.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jBtnDeleteSlotActionPerformed(evt);
      }
    });
    jPanel3.add(jBtnDeleteSlot);

    jPanel1.add(jPanel3, java.awt.BorderLayout.SOUTH);

    jSplitPane2.setBottomComponent(jPanel1);

    kbEditor.setLeftComponent(jSplitPane2);

    jPanelGraph.setMinimumSize(new java.awt.Dimension(540, 540));
    jPanelGraph.setPreferredSize(new java.awt.Dimension(540, 540));
    jPanelGraph.setLayout(new javax.swing.BoxLayout(jPanelGraph, javax.swing.BoxLayout.LINE_AXIS));
    kbEditor.setRightComponent(jPanelGraph);

    jMainPanel.add(kbEditor, "kbEditor");

    getContentPane().add(jMainPanel, java.awt.BorderLayout.CENTER);

    jPanelToolBar.setBorder(javax.swing.BorderFactory.createEtchedBorder());
    jPanelToolBar.setLayout(new java.awt.FlowLayout(java.awt.FlowLayout.LEADING, 0, 0));

    jToolBarFile.setFloatable(false);
    jToolBarFile.setRollover(true);

    jButtonNew.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/new.png"))); // NOI18N
    jButtonNew.setFocusable(false);
    jButtonNew.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonNew.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonNew.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonNewActionPerformed(evt);
      }
    });
    jToolBarFile.add(jButtonNew);

    jButtonOpen.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/open.png"))); // NOI18N
    jButtonOpen.setFocusable(false);
    jButtonOpen.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonOpen.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonOpen.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonOpenActionPerformed(evt);
      }
    });
    jToolBarFile.add(jButtonOpen);

    jButtonSave.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/save.png"))); // NOI18N
    jButtonSave.setFocusable(false);
    jButtonSave.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonSave.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonSave.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonSaveActionPerformed(evt);
      }
    });
    jToolBarFile.add(jButtonSave);

    jPanelToolBar.add(jToolBarFile);
    jPanelToolBar.add(jSeparator7);

    jToolBarGraph.setFloatable(false);
    jToolBarGraph.setRollover(true);
    jToolBarGraph.setCursor(new java.awt.Cursor(java.awt.Cursor.DEFAULT_CURSOR));

    jButtonAdd.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/add.png"))); // NOI18N
    jButtonAdd.setFocusable(false);
    jButtonAdd.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonAdd.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonAdd.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonAddActionPerformed(evt);
      }
    });
    jToolBarGraph.add(jButtonAdd);

    jButtonRemove.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/remove.png"))); // NOI18N
    jButtonRemove.setEnabled(false);
    jButtonRemove.setFocusable(false);
    jButtonRemove.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonRemove.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonRemove.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonRemoveActionPerformed(evt);
      }
    });
    jToolBarGraph.add(jButtonRemove);
    jToolBarGraph.add(jSeparator1);

    jButtonZoomIn.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/zoom in.png"))); // NOI18N
    jButtonZoomIn.setFocusable(false);
    jButtonZoomIn.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonZoomIn.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonZoomIn.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonZoomInActionPerformed(evt);
      }
    });
    jToolBarGraph.add(jButtonZoomIn);

    jButtonZoomOut.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/zoom out.png"))); // NOI18N
    jButtonZoomOut.setFocusable(false);
    jButtonZoomOut.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonZoomOut.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonZoomOut.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonZoomOutActionPerformed(evt);
      }
    });
    jToolBarGraph.add(jButtonZoomOut);
    jToolBarGraph.add(jSeparator3);

    jButtonGroup.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/group.png"))); // NOI18N
    jButtonGroup.setEnabled(false);
    jButtonGroup.setFocusable(false);
    jButtonGroup.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonGroup.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonGroup.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonGroupActionPerformed(evt);
      }
    });
    jToolBarGraph.add(jButtonGroup);

    jButtonUnGroup.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/ungroup.png"))); // NOI18N
    jButtonUnGroup.setEnabled(false);
    jButtonUnGroup.setFocusable(false);
    jButtonUnGroup.setHorizontalTextPosition(javax.swing.SwingConstants.CENTER);
    jButtonUnGroup.setVerticalTextPosition(javax.swing.SwingConstants.BOTTOM);
    jButtonUnGroup.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jButtonUnGroupActionPerformed(evt);
      }
    });
    jToolBarGraph.add(jButtonUnGroup);

    jPanelToolBar.add(jToolBarGraph);

    getContentPane().add(jPanelToolBar, java.awt.BorderLayout.NORTH);

    jMenuFile.setText("Файл");

    jMenuFileItemNew.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_N, java.awt.event.InputEvent.CTRL_MASK));
    jMenuFileItemNew.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/new.png"))); // NOI18N
    jMenuFileItemNew.setText("Новый");
    jMenuFileItemNew.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuFileItemNewActionPerformed(evt);
      }
    });
    jMenuFile.add(jMenuFileItemNew);

    jMenuFileItemOpen.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_O, java.awt.event.InputEvent.CTRL_MASK));
    jMenuFileItemOpen.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/open.png"))); // NOI18N
    jMenuFileItemOpen.setText("Открыть...");
    jMenuFileItemOpen.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuFileItemOpenActionPerformed(evt);
      }
    });
    jMenuFile.add(jMenuFileItemOpen);

    jMenuFileItemSave.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_S, java.awt.event.InputEvent.CTRL_MASK));
    jMenuFileItemSave.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/save.png"))); // NOI18N
    jMenuFileItemSave.setText("Сохранить");
    jMenuFileItemSave.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuFileItemSaveActionPerformed(evt);
      }
    });
    jMenuFile.add(jMenuFileItemSave);

    jMenuFileItemSaveAs.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_S, java.awt.event.InputEvent.SHIFT_MASK | java.awt.event.InputEvent.CTRL_MASK));
    jMenuFileItemSaveAs.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/save_as2.png"))); // NOI18N
    jMenuFileItemSaveAs.setText("Сохранить как...");
    jMenuFileItemSaveAs.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuFileItemSaveAsActionPerformed(evt);
      }
    });
    jMenuFile.add(jMenuFileItemSaveAs);

    jMenuFileItemExport.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_E, java.awt.event.InputEvent.CTRL_MASK));
    jMenuFileItemExport.setText("Экспорт в frs...");
    jMenuFileItemExport.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuFileItemExportActionPerformed(evt);
      }
    });
    jMenuFile.add(jMenuFileItemExport);

    jMenuFileItemClose.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_W, java.awt.event.InputEvent.CTRL_MASK));
    jMenuFileItemClose.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/close.png"))); // NOI18N
    jMenuFileItemClose.setText("Закрыть");
    jMenuFileItemClose.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuFileItemCloseActionPerformed(evt);
      }
    });
    jMenuFile.add(jMenuFileItemClose);
    jMenuFile.add(jSeparator5);

    jMenuFileItemExit.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_Q, java.awt.event.InputEvent.CTRL_MASK));
    jMenuFileItemExit.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/exit.png"))); // NOI18N
    jMenuFileItemExit.setText("Выход");
    jMenuFileItemExit.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuFileItemExitActionPerformed(evt);
      }
    });
    jMenuFile.add(jMenuFileItemExit);

    jMenuBar.add(jMenuFile);

    jMenuEdit.setText("Правка");

    jMenuEditItemDomens.setText("Домены");
    jMenuEditItemDomens.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuEditItemDomensActionPerformed(evt);
      }
    });
    jMenuEdit.add(jMenuEditItemDomens);

    jMenuBar.add(jMenuEdit);

    jMenuHelp.setText("Справка");

    jMenuHelpItemContents.setAccelerator(javax.swing.KeyStroke.getKeyStroke(java.awt.event.KeyEvent.VK_F1, 0));
    jMenuHelpItemContents.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/help.png"))); // NOI18N
    jMenuHelpItemContents.setText("Содержание");
    jMenuHelpItemContents.addActionListener(new java.awt.event.ActionListener() {
      public void actionPerformed(java.awt.event.ActionEvent evt) {
        jMenuHelpItemContentsActionPerformed(evt);
      }
    });
    jMenuHelp.add(jMenuHelpItemContents);
    jMenuHelp.add(jSeparator6);

    jMenuHelpItemAbout.setIcon(new javax.swing.ImageIcon(getClass().getResource("/Images/16x16/about.png"))); // NOI18N
    jMenuHelpItemAbout.setText("О программе");
    jMenuHelp.add(jMenuHelpItemAbout);

    jMenuBar.add(jMenuHelp);

    setJMenuBar(jMenuBar);

    pack();
  }// </editor-fold>//GEN-END:initComponents

    private void jMenuFileItemExitActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jMenuFileItemExitActionPerformed
      exit();
    }//GEN-LAST:event_jMenuFileItemExitActionPerformed
    private void jButtonZoomInActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonZoomInActionPerformed
      double scale = graph.getScale();
      if (scale < MAX_SCALE)
      {
        graph.setScale(scale + STEP_SCALE);
      }
    }//GEN-LAST:event_jButtonZoomInActionPerformed

    private void jButtonZoomOutActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonZoomOutActionPerformed
      double scale = graph.getScale();
      if (scale > MIN_SCALE)
      {
        graph.setScale(scale - STEP_SCALE);
      }
    }//GEN-LAST:event_jButtonZoomOutActionPerformed

  public JTree getOutlinerTree()
  {
    return jOutlinerTree;
  }

    private void jButtonAddActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonAddActionPerformed
      insert(new Point(15, 15));
    }//GEN-LAST:event_jButtonAddActionPerformed

    private void jButtonRemoveActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonRemoveActionPerformed
      remove();
    }//GEN-LAST:event_jButtonRemoveActionPerformed

    private void jButtonGroupActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonGroupActionPerformed
      group(graph.getSelectionCells());
    }//GEN-LAST:event_jButtonGroupActionPerformed

    private void jButtonUnGroupActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonUnGroupActionPerformed
      ungroup(graph.getSelectionCells());
    }//GEN-LAST:event_jButtonUnGroupActionPerformed

    private void formWindowClosing(java.awt.event.WindowEvent evt) {//GEN-FIRST:event_formWindowClosing
      exit();
    }//GEN-LAST:event_formWindowClosing

    private void jMenuFileItemCloseActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jMenuFileItemCloseActionPerformed
      close();
    }//GEN-LAST:event_jMenuFileItemCloseActionPerformed

    private void jMenuFileItemOpenActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jMenuFileItemOpenActionPerformed
      open();
    }//GEN-LAST:event_jMenuFileItemOpenActionPerformed

    private void jButtonOpenActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonOpenActionPerformed
      open();
    }//GEN-LAST:event_jButtonOpenActionPerformed

    private void jButtonSaveActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonSaveActionPerformed
      save();
    }//GEN-LAST:event_jButtonSaveActionPerformed

    private void jMenuFileItemSaveActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jMenuFileItemSaveActionPerformed
      save();
    }//GEN-LAST:event_jMenuFileItemSaveActionPerformed

    private void jMenuFileItemNewActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jMenuFileItemNewActionPerformed
      create();
    }//GEN-LAST:event_jMenuFileItemNewActionPerformed

    private void jButtonNewActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jButtonNewActionPerformed
      create();
    }//GEN-LAST:event_jButtonNewActionPerformed

    private void jMenuFileItemSaveAsActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jMenuFileItemSaveAsActionPerformed
      saveAs();
    }//GEN-LAST:event_jMenuFileItemSaveAsActionPerformed

    private void jBtnInsertSlotActionPerformed(java.awt.event.ActionEvent evt)//GEN-FIRST:event_jBtnInsertSlotActionPerformed
    {//GEN-HEADEREND:event_jBtnInsertSlotActionPerformed
      showSlotCreationForm();
    }//GEN-LAST:event_jBtnInsertSlotActionPerformed

    private void jBtnDeleteSlotActionPerformed(java.awt.event.ActionEvent evt)//GEN-FIRST:event_jBtnDeleteSlotActionPerformed
    {//GEN-HEADEREND:event_jBtnDeleteSlotActionPerformed
      Slot slot = getActiveSlot();

      if (slot == null)
      {
        return;
      }

      if (JOptionPane.showConfirmDialog(this,
              "Удалить выбраный слот (" + slot.getName() + ")?",
              "Подтверждение удаления", JOptionPane.YES_NO_OPTION,
              JOptionPane.QUESTION_MESSAGE) == JOptionPane.YES_OPTION)
      {
        if (!slot.getParent().removeSlot(slot))
        {
          JOptionPane.showMessageDialog(this, "Данный слот используется в "
                  + "одном или нескольких правилах. Удаление невозможно",
                  "Ошибка", JOptionPane.ERROR_MESSAGE);
          return;
        }
        Options.GroupEntry slotParent = getActiveSlotEntry();
        expandedTreeObjects.remove(slotParent.toString());
        updateAll();
      }
    }//GEN-LAST:event_jBtnDeleteSlotActionPerformed

    private void jBtnEditSlotActionPerformed(java.awt.event.ActionEvent evt)//GEN-FIRST:event_jBtnEditSlotActionPerformed
    {//GEN-HEADEREND:event_jBtnEditSlotActionPerformed
      showSlotEditingForm();
    }//GEN-LAST:event_jBtnEditSlotActionPerformed

    private void jMenuEditItemDomensActionPerformed(java.awt.event.ActionEvent evt)//GEN-FIRST:event_jMenuEditItemDomensActionPerformed
    {//GEN-HEADEREND:event_jMenuEditItemDomensActionPerformed
      ChangeDomenDialog dialog = new ChangeDomenDialog(this, true);
      dialog.run(null, null);
    }//GEN-LAST:event_jMenuEditItemDomensActionPerformed

    private void formWindowOpened(java.awt.event.WindowEvent evt) {//GEN-FIRST:event_formWindowOpened
      kbEditor.setVisible(true);
    }//GEN-LAST:event_formWindowOpened

    private void jMenuHelpItemContentsActionPerformed(java.awt.event.ActionEvent evt)//GEN-FIRST:event_jMenuHelpItemContentsActionPerformed
    {//GEN-HEADEREND:event_jMenuHelpItemContentsActionPerformed
      HelpSystem.getInstance().showContents(this);
    }//GEN-LAST:event_jMenuHelpItemContentsActionPerformed

    private void jMenuFileItemExportActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_jMenuFileItemExportActionPerformed
      export();
    }//GEN-LAST:event_jMenuFileItemExportActionPerformed

  private String genFrameName()
  {
    String res = "New frame ";
    int i = 0;
    Frame f;
    while (true)
    {
      f = Frameset.getInstance().getFrameByName(res + i);
      if (f == null)
      {
        res = res + i;
        break;
      }
      i++;
    }
    return res;
  }

  /**
   * Insert frame in point
   * @param point - point to insert frame
   */
  public void insert(Point2D point)
  {
    Frame frame = Frameset.getInstance().createFrame(genFrameName());
    double scale = graph.getScale();
    point.setLocation(point.getX() / scale, point.getY() / scale);
    if (frame != null)
    {
      insert(point, frame);
    }
  }

  /**
   * Insert frame in point
   * @param frame - frame to be added
   */
  public void insert(Frame frame)
  {
    insert(new Point(15, 15), frame);
  }

  /**
   * Insert frame in point
   * @param point - point to insert frame
   * @param frame - frame to be added
   */
  public void insert(Point2D point, Frame frame)
  {
    DefaultGraphCell vertex = createDefaultGraphCell(frame);
    vertex.getAttributes().applyMap(createCellAttributes(point));
    graph.getGraphLayoutCache().insert(vertex);
    graph.setSelectionCell(vertex);
  }

  private DefaultGraphCell createDefaultGraphCell(Frame frame)
  {
    DefaultGraphCell cell = new DefaultGraphCell(frame);
    cell.addPort();
    return cell;
  }

  private Map createCellAttributes(Point2D point)
  {
    Map map = new HashMap();
    if (graph != null)
    {
      point = graph.snap((Point2D) point.clone());
    } else
    {
      point = (Point2D) point.clone();
    }
    GraphConstants.setBounds(map, new Rectangle2D.Double(point.getX(),
            point.getY(), 0, 0));
    GraphConstants.setResize(map, true);
    GraphConstants.setGradientColor(map, Color.green);
    GraphConstants.setBorderColor(map, Color.black);
    GraphConstants.setBackground(map, Color.white);
    GraphConstants.setOpaque(map, true);
    return map;
  }

  private void remove()
  {
    if (!graph.isSelectionEmpty())
    {
      if (JOptionPane.showConfirmDialog(this,
              "Выделенные объекты будут удалены. Продолжить?",
              "Подтверждение удаления", JOptionPane.YES_NO_OPTION,
              JOptionPane.WARNING_MESSAGE) == JOptionPane.YES_OPTION)
      {
        ArrayList<Object> del = new ArrayList<Object>();
        Object[] cells = graph.getSelectionCells();
        for (Object o : cells)
        {
          if (o instanceof DefaultEdge)
          {
            del.add(o);
          } else if (o instanceof DefaultGraphCell)
          {
            DefaultGraphCell dgc = (DefaultGraphCell) o;
            DefaultPort dp = (DefaultPort) ((DefaultGraphCell) o).getChildren().get(0);
            Iterator i = dp.edges();
            while (i.hasNext())
            {
              del.add(i.next());
            }
            del.add(dgc);
            del.add(dp);
          }
        }

        for (Object c : del.toArray())
        {
          if (c instanceof DefaultEdge)
          {
            DefaultEdge de = (DefaultEdge) c;
            Link l = (Link) de.getUserObject();
            Frameset.getInstance().removeLink(l.getSource(), l.getTarget());
          } else if (c instanceof DefaultGraphCell)
          {
            DefaultGraphCell cell = (DefaultGraphCell) c;
            Frame f = (Frame) cell.getUserObject();
            Frameset.getInstance().deleteFrame(f);
          }
        }
        graph.getModel().remove(del.toArray());
      }
    }
  }

  /**
   * // Create a Group that Contains the Cells
   * @param cells - cells to be grouped
   */
  private void group(Object[] cells)
  {
    cells = graph.order(cells);
    if (cells != null && cells.length > 0)
    {
      DefaultGraphCell group = new DefaultGraphCell();
      graph.getGraphLayoutCache().insertGroup(group, cells);
    }
  }

  public Options.GroupEntry getActiveSlotEntry()
  {
    Frame activeFrame = Frameset.getInstance().getActiveFrame();

    if (activeFrame == null)
    {
      return null;
    }

    TreePath path = treeTable.getTreeTableCellRenderer().getSelectionPath();

    if (path == null)
    {
      return null;
    }

    Object[] objPath = path.getPath();
    Object slotsParent = treeTableOptions.getSlotsParent();
    treeTable.getModel();

    for (int i = 0; i < objPath.length; ++i)
    {
      if (objPath[i] == slotsParent)
      {
        if (i < objPath.length - 1)
        {
          Options.GroupEntry slotParent = (Options.GroupEntry) objPath[i + 1];
          return slotParent;
        }

        break;
      }
    }

    return null;
  }

  public Slot getActiveSlot()
  {
    Frame activeFrame = Frameset.getInstance().getActiveFrame();
    Options.GroupEntry slotParent = getActiveSlotEntry();

    if (slotParent == null)
    {
      return null;
    }

    TreePath path = treeTable.getTreeTableCellRenderer().getSelectionPath();

    if (path == null)
    {
      return null;
    }

    return activeFrame.getOwnSlotByName((String) slotParent.getTag());
  }

  public void checkButtons()
  {
    Slot activeSlot = getActiveSlot();

    jBtnInsertSlot.setEnabled(Frameset.getInstance().getActiveFrame() != null);
    jBtnEditSlot.setEnabled(activeSlot != null);
    jBtnDeleteSlot.setEnabled(activeSlot != null);
  }

  /**
   * Ungroup the Groups in Cells and Select the Children
   */
  private void ungroup(Object[] cells)
  {
    graph.getGraphLayoutCache().ungroup(cells);
  }

  public void showSlotCreationForm()
  {
    SlotDialog slotDialog = new SlotDialog(this, true, null, true);
    slotDialog.setVisible(true);
    updateAll();
  }

  public void showSlotEditingForm()
  {
    SlotDialog slotDialog = new SlotDialog(this, true, this.getActiveSlot(), true);
    slotDialog.setVisible(true);
    updateAll();
  }

  public void updateAll()
  {
    updateOptionsModel();

    graph.updateUI();
    jOutlinerTree.updateUI();
    //treeTable.updateUI();
    checkButtons();
  }

  public void insertEdge(Frame source, Frame target, int type, Link l)
  {
    Object[] roots = graph.getRoots();
    DefaultGraphCell from = null, to = null;
    for (Object o : roots)
    {
      if (o instanceof DefaultEdge)
      {
      } else if (o instanceof DefaultGraphCell)
      {
        DefaultGraphCell dgc = (DefaultGraphCell) o;
        if (dgc.toString().equals(source.getName()))
        {
          from = dgc;
        } else if (dgc.toString().equals(target.getName()))
        {
          to = dgc;
        }
      }
      if (from != null && to != null)
      {
        break;
      }
    }
    PortView f = graph.getDefaultPortForCell(from);
    PortView t = graph.getDefaultPortForCell(to);
    MarqueeHandler mh = new MarqueeHandler();
    DefaultEdge edge = mh.createDefaultEdge(type);
    edge.setUserObject(l);
    edge.getAttributes().applyMap(mh.createEdgeAttributes(type));
    graph.getGraphLayoutCache().insertEdge(edge, (Port) f.getCell(), (Port) t.getCell());
  }

  public void removeEdge(Link l)
  {
    Object[] roots = graph.getRoots();
    // Среди всех объектов в графе
    for (Object o : roots)
    {
      // Ищем ребра
      if (o instanceof DefaultEdge)
      {
        DefaultEdge de = (DefaultEdge) o;
        Link edge = (Link) de.getUserObject();
        // Если ребро представляет нужную нам связь
        if (edge == l)
        {
          graph.getModel().remove(new Object[]
                  {
                    de
                  });
          break;
        }
      }
    }
  }
  // Variables declaration - do not modify//GEN-BEGIN:variables
  private javax.swing.ButtonGroup buttonGroup1;
  private javax.swing.JButton jBtnDeleteSlot;
  private javax.swing.JButton jBtnEditSlot;
  private javax.swing.JButton jBtnInsertSlot;
  private javax.swing.JButton jButtonAdd;
  private javax.swing.JButton jButtonGroup;
  private javax.swing.JButton jButtonNew;
  private javax.swing.JButton jButtonOpen;
  private javax.swing.JButton jButtonRemove;
  private javax.swing.JButton jButtonSave;
  private javax.swing.JButton jButtonUnGroup;
  private javax.swing.JButton jButtonZoomIn;
  private javax.swing.JButton jButtonZoomOut;
  private javax.swing.JPanel jMainPanel;
  private javax.swing.JMenuBar jMenuBar;
  private javax.swing.JMenu jMenuEdit;
  private javax.swing.JMenuItem jMenuEditItemDomens;
  private javax.swing.JMenu jMenuFile;
  private javax.swing.JMenuItem jMenuFileItemClose;
  private javax.swing.JMenuItem jMenuFileItemExit;
  private javax.swing.JMenuItem jMenuFileItemExport;
  private javax.swing.JMenuItem jMenuFileItemNew;
  private javax.swing.JMenuItem jMenuFileItemOpen;
  private javax.swing.JMenuItem jMenuFileItemSave;
  private javax.swing.JMenuItem jMenuFileItemSaveAs;
  private javax.swing.JMenu jMenuHelp;
  private javax.swing.JMenuItem jMenuHelpItemAbout;
  private javax.swing.JMenuItem jMenuHelpItemContents;
  private javax.swing.JPanel jOptionsPanel;
  private javax.swing.JTree jOutlinerTree;
  private javax.swing.JPanel jPanel1;
  private javax.swing.JPanel jPanel3;
  private javax.swing.JPanel jPanelGraph;
  private javax.swing.JPanel jPanelToolBar;
  private javax.swing.JScrollPane jScrollPane1;
  private javax.swing.JToolBar.Separator jSeparator1;
  private javax.swing.JToolBar.Separator jSeparator3;
  private javax.swing.JPopupMenu.Separator jSeparator5;
  private javax.swing.JPopupMenu.Separator jSeparator6;
  private javax.swing.JToolBar.Separator jSeparator7;
  private javax.swing.JSplitPane jSplitPane2;
  private javax.swing.JPanel jStatusBar;
  private javax.swing.JLabel jStatusLabel;
  private javax.swing.JToolBar jToolBarFile;
  private javax.swing.JToolBar jToolBarGraph;
  private javax.swing.JSplitPane kbEditor;
  // End of variables declaration//GEN-END:variables

  // MarqueeHandler that Connects Vertices and Displays PopupMenus
  private class MarqueeHandler extends BasicMarqueeHandler
  {

    // Holds the Start and the Current Point
    protected Point2D start, current;
    // Holds the First and the Current Port
    protected PortView port, firstPort;
    protected Object fromCell, toCell;
    /**
     * Component that is used for highlighting cells if
     * the graph does not allow XOR painting.
     */
    protected JComponent highlight = new JPanel();

    public MarqueeHandler()
    {
      // Configures the panel for highlighting ports
      highlight = createHighlight();
    }

    // Insert a new Edge between source and target
    public void connect(Port source, Port target, int type)
    {
      // Construct Edge with specific type
      DefaultEdge edge = createDefaultEdge(type);
      if (graph.getModel().acceptsSource(edge, source)
              && graph.getModel().acceptsTarget(edge, target))
      {
        Frame src = (Frame) ((DefaultGraphCell) (((DefaultPort) source).getParent())).getUserObject();
        Object trg = ((DefaultGraphCell) (((DefaultPort) target).getParent())).getUserObject();
        try
        {
          Link l = Frameset.getInstance().createLink(src, trg, type);
          // Create a map thath holds the attributes for the edge
          edge.getAttributes().applyMap(createEdgeAttributes(type));
          edge.setUserObject(l);
          // Insert the edge and its attributes
          graph.getGraphLayoutCache().insertEdge(edge, source, target);
        } catch (Exception e)
        {
          _System.getInstance().showErrorMsg(e.getMessage());
          graph.refresh();
        }
      }
    }

    protected DefaultEdge createDefaultEdge(int type)
    {
      String name = "";
      switch (type)
      {
        case Link.IS_A:
          name = "is a";
          break;
        case Link.SUB_FRAME:
          name = "sub frame";
          break;
      }
      DefaultEdge de = new DefaultEdge(name);
      return de;
    }

    public Map createEdgeAttributes(int type)
    {
      Map map = new HashMap();
      // Add a Line End Attribute
      GraphConstants.setLineEnd(map, GraphConstants.ARROW_SIMPLE);
      // Add a label along edge attribute
      GraphConstants.setLabelAlongEdge(map, true);
      //Set label non editable
      GraphConstants.setEditable(map, false);
      return map;
    }

    /**
     * Creates the component that is used for highlighting cells if
     * the graph does not allow XOR painting.
     */
    protected JComponent createHighlight()
    {
      JPanel panel = new JPanel();
      panel.setBorder(BorderFactory.createBevelBorder(BevelBorder.RAISED));
      panel.setVisible(false);
      panel.setOpaque(false);
      return panel;
    }

    // Override to Gain Control (for PopupMenu and ConnectMode)
    @Override
    public boolean isForceMarqueeEvent(MouseEvent e)
    {
      if (e.isShiftDown())
      {
        return false;
      }
      // If Right Mouse Button we want to Display the PopupMenu
      if (SwingUtilities.isRightMouseButton(e)) // Return Immediately
      {
        return true;
      }
      // Find and Remember Port
      port = getSourcePortAt(e.getPoint());
      // If Port Found and in ConnectMode (=Ports Visible)
      if (port != null && graph.isPortsVisible())
      {
        return true;
      }
      // Else Call Superclass
      return super.isForceMarqueeEvent(e);
    }

    private JPopupMenu createActionPopupMenu(final Point pt, final Object cell)
    {
      JPopupMenu res = new JPopupMenu();

      if (cell != null)
      {
        if (!(cell instanceof DefaultEdge))
        {
          // Edit
          res.add(new AbstractAction("Редактировать", new ImageIcon("src/Images/16x16/edit.png"))
          {

            @Override
            public void actionPerformed(ActionEvent e)
            {
              graph.startEditingAtCell(cell);
            }
          });

          // Add slot
          res.add(new AbstractAction("Добавить слот", new ImageIcon(""))
          {

            @Override
            public void actionPerformed(ActionEvent e)
            {
              SlotDialog slotDialog = new SlotDialog(_System.getInstance().getMainForm(), true, null, true);
              slotDialog.setVisible(true);
              updateAll();
            }
          });
        }

        // Remove
        res.add(new AbstractAction("Удалить", new ImageIcon("src/Images/16x16/remove.png"))
        {

          @Override
          public void actionPerformed(ActionEvent e)
          {
            remove();
          }
        });
      } else
      {
        // Add
        res.add(new AbstractAction("Добавить", new ImageIcon("src/Images/16x16/add.png"))
        {

          @Override
          public void actionPerformed(ActionEvent ev)
          {
            insert(pt);
          }
        });
      }
      return res;
    }

    // Display PopupMenu or Remember Start Location and First Port
    @Override
    public void mousePressed(final MouseEvent e)
    {
      if (SwingUtilities.isRightMouseButton(e))
      {
        Object cell = graph.getFirstCellForLocation(e.getX(), e.getY());
        JPopupMenu menu = createActionPopupMenu(e.getPoint(), cell);
        menu.show(graph, e.getX(), e.getY());
      } else if (port != null && graph.isPortsVisible())
      {
        // Remember Start Location and First Port
        start = graph.toScreen(port.getLocation());
        firstPort = port;
      } else
      {
        super.mousePressed(e);
      }
    }

    // Find Port under Mouse and Repaint Connector
    @Override
    public void mouseDragged(MouseEvent e)
    {
      if (start != null)
      {
        Graphics g = graph.getGraphics();
        PortView newPort = getTargetPortAt(e.getPoint());
        // Do not flicker (repaint only on real changes)
        if (newPort == null || newPort != port)
        {
          // Xor-Paint the old Connector (Hide old Connector)
          paintConnector(Color.black, graph.getBackground(), g);
          // If Port was found then Point to Port Location
          port = newPort;
          if (port != null)
          {
            current = graph.toScreen(port.getLocation());
          } // Else If no Port was found then Point to Mouse Location
          else
          {
            current = graph.snap(e.getPoint());
          }
          // Xor-Paint the new Connector
          paintConnector(graph.getBackground(), Color.black, g);
        }
      }
      super.mouseDragged(e);
    }

    public PortView getSourcePortAt(Point2D point)
    {
      // Disable jumping
      graph.setJumpToDefaultPort(false);
      PortView result;
      try
      {
        // Find a Port View in Model Coordinates and Remember
        result = graph.getPortViewAt(point.getX(), point.getY());
      } finally
      {
        graph.setJumpToDefaultPort(true);
      }
      return result;
    }

    // Find a Cell at point and Return its first Port as a PortView
    protected PortView getTargetPortAt(Point2D point)
    {
      // Find a Port View in Model Coordinates and Remember
      return graph.getPortViewAt(point.getX(), point.getY());
    }

    private JPopupMenu createEdgesPopupMenu(final Object cellFrom, final Object cellTo)
    {
      JPopupMenu res = new JPopupMenu();

      res.add(new AbstractAction("is a")
      {

        @Override
        public void actionPerformed(ActionEvent e)
        {
          connect((Port) cellFrom, (Port) cellTo, Link.IS_A);
        }
      });
      res.add(new AbstractAction("sub frame")
      {

        @Override
        public void actionPerformed(ActionEvent e)
        {
          Frame src = (Frame) ((DefaultGraphCell) (((DefaultPort) cellFrom).getParent())).getUserObject();
          Object trg = ((DefaultGraphCell) (((DefaultPort) cellTo).getParent())).getUserObject();
          if (trg instanceof Frame)
          {
            SlotDialog slotDialog = new SlotDialog(_System.getInstance().getMainForm(), src, (Frame) trg);
            slotDialog.setVisible(true);
            updateAll();
          }
        }
      });
      res.addMenuKeyListener(new MenuKeyListener()
      {

        @Override
        public void menuKeyTyped(MenuKeyEvent e)
        {
        }

        @Override
        public void menuKeyPressed(MenuKeyEvent e)
        {
          // Update graph when Escape pressed
          if (e.getKeyCode() == KeyEvent.VK_ESCAPE)
          {
            graph.repaint();
          }
        }

        @Override
        public void menuKeyReleased(MenuKeyEvent e)
        {
        }
      });
      return res;
    }

    // Connect the First Port and the Current Port in the Graph or Repaint
    @Override
    public void mouseReleased(MouseEvent e)
    {
      highlight(graph, null);

      // If Valid Event, Current and First Port
      if (e != null && port != null && firstPort != null
              && firstPort != port)
      {
        // Then Establish Connection
        JPopupMenu menu = createEdgesPopupMenu(firstPort.getCell(),
                port.getCell());
        menu.show(graph, e.getX(), e.getY());
        e.consume();
        // Else Repaint the Graph
      } else
      {
        graph.repaint();
      }
      // Reset Global Vars
      firstPort = port = null;
      start = current = null;
      super.mouseReleased(e);
    }

    // Show Special Cursor if Over Port
    @Override
    public void mouseMoved(MouseEvent e)
    {
      // Check Mode and Find Port
      if (e != null && getSourcePortAt(e.getPoint()) != null
              && graph.isPortsVisible())
      {
        // Set Cusor on Graph (Automatically Reset)
        graph.setCursor(new Cursor(Cursor.HAND_CURSOR));
        e.consume();
      } else // Call Superclass
      {
        super.mouseMoved(e);
      }
    }

    // Use Xor-Mode on Graphics to Paint Connector
    protected void paintConnector(Color fg, Color bg, Graphics g)
    {
      if (graph.isXorEnabled())
      {
        // Set Foreground
        g.setColor(fg);
        // Set Xor-Mode Color
        g.setXORMode(bg);
        // Highlight the Current Port
        paintPort(graph.getGraphics());

        drawConnectorLine(g);
      } else
      {
        Rectangle dirty = new Rectangle((int) start.getX(), (int) start.getY(), 1, 1);

        if (current != null)
        {
          dirty.add(current);
        }

        dirty.grow(1, 1);

        graph.repaint(dirty);
        highlight(graph, port);
      }
    }

    // Overrides parent method to paint connector if
    // XOR painting is disabled in the graph
    @Override
    public void paint(JGraph graph, Graphics g)
    {
      super.paint(graph, g);

      if (!graph.isXorEnabled())
      {
        g.setColor(Color.black);
        drawConnectorLine(g);
      }
    }

    protected void drawConnectorLine(Graphics g)
    {
      if (firstPort != null && start != null && current != null)
      {
        // Then Draw A Line From Start to Current Point
        g.drawLine((int) start.getX(), (int) start.getY(),
                (int) current.getX(), (int) current.getY());
      }
    }

    // Use the Preview Flag to Draw a Highlighted Port
    protected void paintPort(Graphics g)
    {
      // If Current Port is Valid
      if (port != null)
      {
        // If Not Floating Port...
        boolean o = (GraphConstants.getOffset(port.getAllAttributes()) != null);
        // ...Then use Parent's Bounds
        Rectangle2D r = (o) ? port.getBounds() : port.getParentView().getBounds();
        // Scale from Model to Screen
        r = graph.toScreen((Rectangle2D) r.clone());
        // Add Space For the Highlight Border
        r.setFrame(r.getX() - 3, r.getY() - 3, r.getWidth() + 6, r.getHeight() + 6);
        // Paint Port in Preview (=Highlight) Mode
        graph.getUI().paintCell(g, port, r, true);
      }
    }

    /**
     * Highlights the given cell view or removes the highlight if
     * no cell view is specified.
     */
    protected void highlight(JGraph graph, CellView cellView)
    {
      if (cellView != null)
      {
        highlight.setBounds(getHighlightBounds(graph, cellView));

        if (highlight.getParent() == null)
        {
          graph.add(highlight);
          highlight.setVisible(true);
        }
      } else
      {
        if (highlight.getParent() != null)
        {
          highlight.setVisible(false);
          highlight.getParent().remove(highlight);
        }
      }
    }

    /**
     * Returns the bounds to be used to highlight the given cell view.
     */
    protected Rectangle getHighlightBounds(JGraph graph, CellView cellView)
    {
      boolean offset = (GraphConstants.getOffset(cellView.getAllAttributes()) != null);
      Rectangle2D r = (offset) ? cellView.getBounds() : cellView.getParentView().getBounds();
      r = graph.toScreen((Rectangle2D) r.clone());
      int s = 3;

      return new Rectangle((int) (r.getX() - s), (int) (r.getY() - s),
              (int) (r.getWidth() + 2 * s), (int) (r.getHeight() + 2 * s));
    }
  }

  private void processTreeExpansion(TreeExpansionEvent e)
  {

    if (supressExpansionEvent == false)
    { // Not interested in this event if we are currently restoring the tree

      TreePath p = e.getPath(); // Get the tree path
      Object[] Objs = p.getPath(); // Get all the objects withiin that path
      DefaultMutableTreeNode dmtn = (DefaultMutableTreeNode) Objs[Objs.length - 1]; // Derive a DMTN from the last object
      String myString = (String) dmtn.getUserObject(); // In this demo, this will always be a String object
      expandedTreeObjects.remove(myString);
      expandedTreeObjects.add(myString); // Place the object in our list
    }

  }

  private void processTreeCollapse(TreeExpansionEvent e)
  {
    TreePath p = e.getPath(); // Get the tree path
    Object[] Objs = p.getPath(); // Get all the objects withiin that path
    DefaultMutableTreeNode dmtn = (DefaultMutableTreeNode) Objs[Objs.length - 1]; // Derive a DMTN
    String myString = (String) dmtn.getUserObject(); // This will always be a String
    expandedTreeObjects.remove(myString);
  }

  private void restoreTree()
  {
    restoreTreeNode(treeTable.getTreeTableCellRenderer(), new TreePath(treeTable.getTreeModel().getRoot()), null);
  }

  private void restoreTreeNode(JTree tree, TreePath parent, DefaultMutableTreeNode treeNode)
  {

    // Traverse down through the children
    TreeNode node = (TreeNode) parent.getLastPathComponent(); // Get the last TreeNode component for this path

    if (node.getChildCount() >= 0)
    { // If the node has children?

      // Create a child numerator over the node
      Enumeration en = node.children();
      while (en.hasMoreElements())
      { // While we have children

        DefaultMutableTreeNode dmTreeNode = (DefaultMutableTreeNode) en.nextElement(); // Derive the node
        TreePath path = parent.pathByAddingChild(dmTreeNode); // Derive the path
        restoreTreeNode(tree, path, dmTreeNode); // Recursive call with new path

      } // End While we have more children

    } // End If the node has children?


    // Nodes need to be expand from last branch node up
    if (treeNode != null)
    { // If true, this is the root node - ignore it

      String myString = (String) treeNode.getUserObject(); // Get the user object from the node
      // Note - all the objects I place in tree nodes in this demo
      // belong to the same class - String

      if (expandedTreeObjects.contains(myString))
      { // Is this present on the previously expanded list?
        tree.expandPath(parent); // et viola
      }

    } // End If - root node

  }
  private List<String> expandedTreeObjects;
  private boolean supressExpansionEvent = false;
}
