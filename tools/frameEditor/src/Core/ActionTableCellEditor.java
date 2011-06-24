/**
 * MySwing: Advanced Swing Utilites
 * Copyright (C) 2005  Santhosh Kumar T
 * <p/>
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * <p/>
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 */
package Core;

import java.awt.BorderLayout;
import java.awt.Component;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.EventObject;
import javax.swing.Icon;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JPanel;
import javax.swing.JTable;
import javax.swing.event.CellEditorListener;
import javax.swing.table.TableCellEditor;

public abstract class ActionTableCellEditor implements TableCellEditor, ActionListener
{

  public final Icon DOTDOTDOT_ICON = new ImageIcon(getClass().getResource("/UI/Forms/Icons/dotdotdot.png"));
  private TableCellEditor editor;
  private JButton customEditorButton = new JButton(DOTDOTDOT_ICON);
  protected JTable table;
  protected int row, column;

  public ActionTableCellEditor(TableCellEditor editor)
  {
    this.editor = editor;
    customEditorButton.addActionListener(this);

    // ui-tweaking
    customEditorButton.setFocusable(false);
    customEditorButton.setFocusPainted(false);
    customEditorButton.setMargin(new Insets(0, 0, 0, 0));
  }

  @Override
  public Component getTableCellEditorComponent(JTable table, Object value, boolean isSelected, int row, int column)
  {
    JPanel panel = new JPanel(new BorderLayout());
    if (editor != null) {
      panel.add(editor.getTableCellEditorComponent(table, value, isSelected, row, column));
      panel.add(customEditorButton, BorderLayout.EAST);
    } else {
      // could it be nicer?
      panel.add(customEditorButton, BorderLayout.EAST);
    }

    this.table = table;
    this.row = row;
    this.column = column;
    return panel;
  }

  @Override
  public Object getCellEditorValue()
  {
    return editor.getCellEditorValue();
  }

  @Override
  public boolean isCellEditable(EventObject anEvent)
  {
    if (editor == null)
      return true;

    return editor.isCellEditable(anEvent);
  }

  @Override
  public boolean shouldSelectCell(EventObject anEvent)
  {
    if (editor == null)
      return false;

    return editor.shouldSelectCell(anEvent);
  }

  @Override
  public boolean stopCellEditing()
  {
    if (editor == null)
      return false;

    return editor.stopCellEditing();
  }

  @Override
  public void cancelCellEditing()
  {
    if (editor != null)
      editor.cancelCellEditing();
  }

  @Override
  public void addCellEditorListener(CellEditorListener l)
  {
    if (editor != null)
      editor.addCellEditorListener(l);
  }

  @Override
  public void removeCellEditorListener(CellEditorListener l)
  {
    if (editor != null)
      editor.removeCellEditorListener(l);
  }

  @Override
  public final void actionPerformed(ActionEvent e)
  {
    if (editor != null)
      editor.cancelCellEditing();

    editCell(table, row, column);
  }

  protected abstract void editCell(JTable table, int row, int column);
}
