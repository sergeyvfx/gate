package UI.Forms.Rule;

import logic.product.Rule;
import logic.frames.Frame;
import logic.frames.Frameset;
import logic.frames.ISlot;
import UI.TextAreaRenderer;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ItemEvent;
import java.awt.event.ItemListener;
import java.awt.event.KeyAdapter;
import java.awt.event.KeyEvent;
import java.util.ArrayList;
import javax.swing.BorderFactory;
import javax.swing.DefaultComboBoxModel;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JComboBox;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.event.ListSelectionEvent;
import javax.swing.event.ListSelectionListener;

public class RulePanel extends JPanel
{

  private RuleTable jTableRule;
  private ArrayList<Rule> rules;
  private JButton jButtonRuleAdd;
  private JButton jButtonRuleDelete;
  private JButton jButtonRuleEdit;
  private JComboBox jComboBoxFrame;
  private JComboBox jComboBoxSlot;
  private java.awt.Frame parent;

  public RulePanel(java.awt.Frame parent, ArrayList<Rule> r)
  {
    this.parent = parent;
    rules = r;
    JPanel rulePanel = new JPanel();
    rulePanel.setLayout(new GridBagLayout());
    rulePanel.setBorder(BorderFactory.createTitledBorder("Правила"));
    JPanel goalPanel = new JPanel();
    goalPanel.setLayout(new GridBagLayout());
    goalPanel.setBorder(BorderFactory.createTitledBorder("Цель"));
    jComboBoxFrame = new JComboBox(Frameset.getInstance().getAllFrames().toArray());
    jComboBoxFrame.addItemListener(new ItemListener()
    {

      @Override
      public void itemStateChanged(ItemEvent e)
      {
        updateJComboBoxSlot();
      }
    });
    jComboBoxSlot = new JComboBox();
    updateJComboBoxSlot();
    goalPanel.add(new JLabel("Фрейм:"), new GridBagConstraints(0, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    goalPanel.add(jComboBoxFrame, new GridBagConstraints(1, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    goalPanel.add(new JLabel("Слот:"), new GridBagConstraints(2, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    goalPanel.add(jComboBoxSlot, new GridBagConstraints(3, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    jTableRule = new RuleTable(new RuleTableModel(rules));
    jTableRule.addKeyListener(new KeyAdapter()
    {

      @Override
      public void keyPressed(KeyEvent evt)
      {
        jTableRuleKeyPressed(evt);
      }
    });
    jTableRule.getSelectionModel().addListSelectionListener(new ListSelectionListener()
    {

      @Override
      public void valueChanged(ListSelectionEvent e)
      {
        updateRuleButtons();
      }
    });
    for (int i = 0; i < jTableRule.getColumnCount(); i++)
    {
      jTableRule.getColumnModel().getColumn(i).setCellRenderer(new TextAreaRenderer());
    }

    jButtonRuleAdd = new JButton("Добавить", new ImageIcon(getClass().getResource("/Images/16x16/add.png")));
    jButtonRuleAdd.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent evt)
      {
        jButtonRuleAddActionPerformed(evt);
      }
    });

    jButtonRuleEdit = new JButton("Изменить", new ImageIcon(getClass().getResource("/Images/16x16/edit.png")));
    jButtonRuleEdit.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent evt)
      {
        jButtonRuleEditActionPerformed(evt);
      }
    });

    jButtonRuleDelete = new JButton("Удалить", new ImageIcon(getClass().getResource("/Images/16x16/remove.png")));
    jButtonRuleDelete.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent evt)
      {
        jButtonRuleDeleteActionPerformed(evt);
      }
    });

    rulePanel.add(new JScrollPane(jTableRule), new GridBagConstraints(0, 0, 3, 1, 1, 1,
            GridBagConstraints.NORTHWEST, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    rulePanel.add(jButtonRuleAdd, new GridBagConstraints(0, 1, 1, 1, 1, 0,
            GridBagConstraints.CENTER, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    rulePanel.add(jButtonRuleEdit, new GridBagConstraints(1, 1, 1, 1, 1, 0,
            GridBagConstraints.CENTER, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    rulePanel.add(jButtonRuleDelete, new GridBagConstraints(2, 1, 1, 1, 1, 0,
            GridBagConstraints.CENTER, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    setLayout(new GridBagLayout());
    add(goalPanel, new GridBagConstraints(0, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(rulePanel, new GridBagConstraints(0, 1, 1, 1, 1, 1,
            GridBagConstraints.NORTH, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
  }

  private void jTableRuleKeyPressed(java.awt.event.KeyEvent evt)
  {
    if (evt.getKeyCode() == KeyEvent.VK_DELETE
            && jTableRule.getSelectedRowCount() == 1)
    {
      jButtonRuleDeleteActionPerformed(null);
    }
    if (evt.getKeyCode() == KeyEvent.VK_ENTER && evt.isControlDown())
    {
      jButtonRuleAddActionPerformed(null);
    }
  }

  private void jButtonRuleAddActionPerformed(java.awt.event.ActionEvent evt)
  {
    RuleDialog rd = new RuleDialog(parent, true);
    rd.run("Добаление правила", null, rules);
    ((RuleTableModel) jTableRule.getModel()).fireTableDataChanged();
    if (rd.getRule() != null)
    {
      int index = rules.indexOf(rd.getRule());
      jTableRule.setRowSelectionInterval(index, index);
      jTableRule.requestFocusInWindow();
    }
  }

  private void jButtonRuleEditActionPerformed(java.awt.event.ActionEvent evt)
  {
    if (jTableRule.getSelectedRowCount() == 0)
    {
      JOptionPane.showMessageDialog(this, "Вы должны выбрать правило перед изменением", "Ошибка",
              JOptionPane.ERROR_MESSAGE);
      return;
    }
    int row = jTableRule.getSelectedRow();
    RuleDialog rd = new RuleDialog(parent, true);
    Rule rule = ((RuleTableModel) jTableRule.getModel()).getValueAt(row);
    rd.run("Изменение правила", rule, rules);
    ((RuleTableModel) jTableRule.getModel()).fireTableDataChanged();
    jTableRule.setRowSelectionInterval(row, row);
    jTableRule.requestFocusInWindow();
  }

  private void jButtonRuleDeleteActionPerformed(java.awt.event.ActionEvent evt)
  {
    if (jTableRule.getSelectedRowCount() == 0)
    {
      JOptionPane.showMessageDialog(this, "Вы должны выбрать правило перед удалением", "Ошибка",
              JOptionPane.ERROR_MESSAGE);
      return;
    }
    int row = jTableRule.getSelectedRow();
    row = jTableRule.convertRowIndexToModel(row);

    int res = JOptionPane.showConfirmDialog(this, "Правило будет удалено. "
            + "Вы уверены?", "Подтверждение удаления",
            JOptionPane.YES_NO_OPTION, JOptionPane.WARNING_MESSAGE);
    if (res == JOptionPane.YES_OPTION)
    {
      rules.remove(row);
      ((RuleTableModel) jTableRule.getModel()).fireTableDataChanged();

      int rowCount = jTableRule.getRowCount();
      int rowToSelect = row == rowCount ? rowCount - 1 : row;
      if (rowToSelect > -1)
      {
        jTableRule.setRowSelectionInterval(rowToSelect, rowToSelect);
        jTableRule.requestFocusInWindow();
      }
      updateRuleButtons();
    }
  }

  private void updateJComboBoxSlot()
  {
    Frame f = (Frame) jComboBoxFrame.getSelectedItem();
    jComboBoxSlot.setModel(new DefaultComboBoxModel(f.getSlots().toArray()));
  }

  private void updateRuleButtons()
  {
    jButtonRuleEdit.setEnabled(jTableRule.getSelectedRowCount() == 1);
    jButtonRuleDelete.setEnabled(jTableRule.getSelectedRowCount() == 1);
  }

  public void setRules(ArrayList<Rule> r)
  {
    rules = r;
    jTableRule.setModel(new RuleTableModel(rules));
  }

  public void setGoalSlot(ISlot s)
  {
    if (s != null)
    {
      jComboBoxFrame.setSelectedItem(s.getParent());
    }
    updateJComboBoxSlot();
    jComboBoxSlot.setSelectedItem(s);
  }

  public ArrayList<Rule> getRules()
  {
    ArrayList<Rule> res = ((RuleTableModel) jTableRule.getModel()).getDataList();
    if (res == null) {
      res = new ArrayList<Rule>();
    }
    return res;
  }

  public ISlot getGoalSlot()
  {
    return (ISlot) jComboBoxSlot.getSelectedItem();
  }
}
