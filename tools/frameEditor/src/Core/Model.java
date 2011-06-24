package Core;

import logic.frames.Frame;
import java.io.Serializable;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.event.TreeModelListener;
import javax.swing.event.UndoableEditListener;
import javax.swing.tree.TreeModel;
import javax.swing.tree.TreePath;
import javax.swing.tree.DefaultTreeModel;
import javax.swing.undo.UndoableEdit;
import org.jgraph.event.GraphModelListener;
import org.jgraph.graph.AttributeMap;
import org.jgraph.graph.ConnectionSet;
import org.jgraph.graph.DefaultGraphModel;
import org.jgraph.graph.Edge;
import org.jgraph.graph.ExecutableChange;
import org.jgraph.graph.GraphModel;
import org.jgraph.graph.ParentMap;

import org.jgraph.graph.DefaultGraphCell;

public class Model implements GraphModel, Serializable, TreeModel/*, TableModel */ {
    private DefaultGraphModel defaultGraphModel;
    private Class<DefaultGraphModel> defaultGraphModelClass;
    private DefaultTreeModel defaultTreeModel;
    private Class<DefaultTreeModel> defaultTreeModelClass;

    public Model() {
        defaultGraphModel = new DefaultGraphModel();
        defaultGraphModelClass = DefaultGraphModel.class;

        defaultTreeModel = new DefaultTreeModel(null);
        defaultTreeModelClass = DefaultTreeModel.class;
    }

    // <editor-fold defaultstate="collapsed" desc="Implementation of GraphModel">
  @Override
    public int getRootCount() {
        int res = 0;
        try {
            Method m = defaultGraphModelClass.getMethod("getRootCount", new Class[]{});
            res = (Integer) m.invoke(defaultGraphModel, new Object[]{});
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public Object getRootAt(int index) {
        Object res = null;
        try {
            Method m = defaultGraphModelClass.getMethod("getRootAt",
                    new Class[]{Integer.TYPE});
            res = m.invoke(defaultGraphModel, new Object[]{index});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public int getIndexOfRoot(Object root) {
        int res = 0;
        try {
            Method m = defaultGraphModelClass.getMethod("getIndexOfRoot",
                    new Class[]{});
            res = (Integer) m.invoke(defaultGraphModel, new Object[]{root});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public boolean contains(Object node) {
        boolean res = false;
        try {
            Method m = defaultGraphModelClass.getMethod("contains",
                    new Class[]{Object.class});
            res = (Boolean) m.invoke(defaultGraphModel, new Object[]{node});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public AttributeMap getAttributes(Object node) {
        AttributeMap res = null;
        try {
            Method m = defaultGraphModelClass.getMethod("getAttributes",
                    new Class[]{Object.class});
            res = (AttributeMap) m.invoke(defaultGraphModel, new Object[]{node});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public Object getValue(Object node) {
        throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public Object getSource(Object edge) {
        Object res = null;
        try {
            Method m = defaultGraphModelClass.getMethod("getSource",
                    new Class[]{Object.class});
            res = m.invoke(defaultGraphModel, new Object[]{edge});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public Object getTarget(Object edge) {
        Object res = null;
        try {
            Method m = defaultGraphModelClass.getMethod("getTarget",
                    new Class[]{Object.class});
            res = m.invoke(defaultGraphModel, new Object[]{edge});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public boolean acceptsSource(Object edge, Object port) {
        return ((Edge) edge).getTarget() != port;
    }

  @Override
    public boolean acceptsTarget(Object edge, Object port) {
        return ((Edge) edge).getSource() != port;
    }

  @Override
    public Iterator edges(Object port) {
        Iterator res = null;
        try {
            Method m = defaultGraphModelClass.getMethod("edges",
                    new Class[]{Object.class});
            res = (Iterator) m.invoke(defaultGraphModel, new Object[]{port});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public boolean isEdge(Object edge) {
        boolean res = false;
        try {
            Method m = defaultGraphModelClass.getMethod("isEdge",
                    new Class[]{Object.class});
            res = (Boolean) m.invoke(defaultGraphModel, new Object[]{edge});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public boolean isPort(Object port) {
        boolean res = false;
        try {
            Method m = defaultGraphModelClass.getMethod("isPort",
                    new Class[]{Object.class});
            res = (Boolean) m.invoke(defaultGraphModel, new Object[]{port});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public Object getParent(Object child) {
        Object res = null;
        try {
            Method m = defaultGraphModelClass.getMethod("getParent",
                    new Class[]{Object.class});
            res = m.invoke(defaultGraphModel, new Object[]{child});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public int getIndexOfChild(Object parent, Object child) {
      try {
        Method m = defaultGraphModelClass.getMethod("getIndexOfChild",
                new Class[]{Object.class, Object.class});
        return (Integer)m.invoke(defaultGraphModel, new Object[]{parent, child});
      } catch (Exception ex) {
          ex.printStackTrace();
      }

      return -1;
    }

    private boolean isOutlinerObject(Object ob) {
      if (ob instanceof DefaultGraphCell) {
        Object user = ((DefaultGraphCell)ob).getUserObject();

        if (user instanceof Frame) {
          return true;
        }
      }

      return false;
    }

    private Object[] getOutlinerChilds(Object parent) {
      List<Object> result = new ArrayList<Object> ();

      if (parent == null || parent instanceof String) {
        for (int i = 0, n = getRootCount(); i < n; ++i) {
          Object ob = getRootAt(i);

          if (isOutlinerObject(ob)) {
              result.add(ob);
          }
        }
      }

      return result.toArray();
    }

  @Override
    public Object getChild(Object parent, int index) {
        Object res = null;

        if (parent instanceof String) {
          return getOutlinerChilds (parent) [index];
        }

        try {
            Method m = defaultGraphModelClass.getMethod("getChild",
                    new Class[]{Object.class, Integer.TYPE});
            res = m.invoke(defaultGraphModel, new Object[]{parent, index});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public int getChildCount(Object parent) {
        int res = 0;

        if (parent instanceof String) {
          return getOutlinerChilds (parent).length;
        }

        try {
            Method m = defaultGraphModelClass.getMethod("getChildCount",
                    new Class[]{Object.class});
            res = (Integer) m.invoke(defaultGraphModel, new Object[]{parent});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
        return res;
    }

  @Override
    public void insert(Object[] roots, Map attributes, ConnectionSet cs, ParentMap pm, UndoableEdit[] e) {
        try {
            Method m = defaultGraphModelClass.getMethod("insert",
                    new Class[]{Object[].class, Map.class, ConnectionSet.class, ParentMap.class, UndoableEdit[].class});
            m.invoke(defaultGraphModel, new Object[]{roots, attributes, cs, pm, e});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
    }

  @Override
    public void remove(Object[] roots) {
        try {
            Method m = defaultGraphModelClass.getMethod("remove",
                    new Class[]{Object[].class});
            m.invoke(defaultGraphModel, new Object[]{roots});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
    }

  @Override
    public void edit(Map attributes, ConnectionSet cs, ParentMap pm, UndoableEdit[] e) {
        try {
            Iterator it = attributes.entrySet().iterator();

            while (it.hasNext()) {
              Map.Entry pairs = (Map.Entry)it.next();
              Object key = pairs.getKey();
              Hashtable ht = (Hashtable) pairs.getValue();

              if (key instanceof DefaultGraphCell &&
                      ((DefaultGraphCell)key).getUserObject() instanceof Frame) {
                String name = (String)ht.get("value");

                if (name != null) {
                  Frame frame = (Frame)((DefaultGraphCell)key).getUserObject();
                  frame.setName(name);
                  ht.remove("value");
                }
              }
            }

            Method m = defaultGraphModelClass.getMethod("edit",
                    new Class[]{Map.class, ConnectionSet.class, ParentMap.class, UndoableEdit[].class});
            m.invoke(defaultGraphModel, new Object[]{attributes, cs, pm, e});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
    }

  @Override
    public void beginUpdate() {
        throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public void endUpdate() {
        throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public void execute(ExecutableChange change) {
        throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public Map cloneCells(Object[] cells) {
        throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public Object valueForCellChanged(Object cell, Object newValue) {
        throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public void toBack(Object[] cells) {
        throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public void toFront(Object[] cells) {
        throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public void addGraphModelListener(GraphModelListener l) {
        try {
            Method m = defaultGraphModelClass.getMethod("addGraphModelListener",
                    new Class[]{GraphModelListener.class});
            m.invoke(defaultGraphModel, new Object[]{l});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
    }

  @Override
    public void removeGraphModelListener(GraphModelListener l) {
        try {
            Method m = defaultGraphModelClass.getMethod("removeGraphModelListener",
                    new Class[]{GraphModelListener.class});
            m.invoke(defaultGraphModel, new Object[]{l});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
    }

  @Override
    public void addUndoableEditListener(UndoableEditListener listener) {
        try {
            Method m = defaultGraphModelClass.getMethod("addUndoableEditListener",
                    new Class[]{UndoableEditListener.class});
            m.invoke(defaultGraphModel, new Object[]{listener});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
    }

  @Override
    public void removeUndoableEditListener(UndoableEditListener listener) {
        try {
            Method m = defaultGraphModelClass.getMethod("removeUndoableEditListener",
                    new Class[]{UndoableEditListener.class});
            m.invoke(defaultGraphModel, new Object[]{listener});
        } catch (NoSuchMethodException ex) {
            ex.printStackTrace();
        } catch (SecurityException ex) {
            ex.printStackTrace();
        } catch (IllegalAccessException ex) {
            ex.printStackTrace();
        } catch (IllegalArgumentException ex) {
            ex.printStackTrace();
        } catch (InvocationTargetException ex) {
            ex.printStackTrace();
        }
    }
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Implementation of TreeModel">
  @Override
    public Object getRoot()
    {
      return "Навигатор";
    }

  @Override
    public void valueForPathChanged(TreePath path, Object newValue)
    {
      throw new UnsupportedOperationException("Not supported yet.");
    }

  @Override
    public void addTreeModelListener(TreeModelListener l)
    {
      try {
        Method m = defaultTreeModelClass.getMethod("addTreeModelListener", new Class[]{TreeModelListener.class});
        m.invoke(defaultTreeModel, new Object[]{l});
      } catch (Exception ex) {
        Logger.getLogger(Model.class.getName()).log(Level.SEVERE, null, ex);
      }
    }

  @Override
    public void removeTreeModelListener(TreeModelListener l)
    {
      try {
        Method m = defaultTreeModelClass.getMethod("removeTreeModelListener", new Class[]{TreeModelListener.class});
        m.invoke(defaultTreeModel, new Object[]{l});
      } catch (Exception ex) {
        Logger.getLogger(Model.class.getName()).log(Level.SEVERE, null, ex);
      }
    }

  @Override
    public boolean isLeaf(Object node) {
      if (node instanceof String) {
        return false;
      }

      return true;
    }

    public TreePath getTreePath(Object ob) {
      if (ob != null && isOutlinerObject(ob)) {
        // XXX: Will work incorrect when real depgraph will be implemented
        return new TreePath(new Object[] {this.getRoot(), ob});
      } else {
        return new TreePath(new Object[] {this.getRoot()});
      }
    }
    // </editor-fold>
}
