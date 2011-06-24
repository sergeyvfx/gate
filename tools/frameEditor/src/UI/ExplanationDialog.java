/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package UI;

import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import javax.swing.JDialog;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTree;
import javax.swing.tree.DefaultMutableTreeNode;

/**
 *
 * @author nazgul
 */
public class ExplanationDialog extends JDialog
{
  private DefaultMutableTreeNode root;

  public ExplanationDialog(java.awt.Frame parent, DefaultMutableTreeNode root)
  {
    super(parent);

    this.root = root;

    CreateUI();
  }

  private void CreateUI()
  {
    setTitle("Компонента объяснения");

    setLayout(new GridBagLayout());

    JPanel content = new JPanel(new GridBagLayout());
    JLabel lbl = new JLabel("Ход вывода");

    content.add(lbl, new GridBagConstraints(0, 0, 1, 1, 0, 0, GridBagConstraints.NORTHWEST,
            GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));

    JScrollPane jspFull = new JScrollPane();
    JTree jtFull = new JTree(root);
    jspFull.setViewportView(jtFull);

    content.add(jspFull, new GridBagConstraints(0, 1, 1, 1, 1, 1, GridBagConstraints.NORTH,
            GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));

    getContentPane().add(content, new GridBagConstraints(0, 0, 1, 1, 1, 1, GridBagConstraints.NORTHWEST,
            GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));

    setSize(540, 746);

    setLocation((getParent().getWidth() - getWidth())
            + getParent().getX(), (getParent().getHeight()
            - getHeight()) / 2 + getParent().getY());
  }
}
