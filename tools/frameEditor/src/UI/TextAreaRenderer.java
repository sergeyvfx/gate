package UI;

import javax.swing.*;
import javax.swing.table.TableCellRenderer;
import java.awt.*;

public class TextAreaRenderer extends JTextArea implements TableCellRenderer {

    public TextAreaRenderer() {
        setLineWrap(true);
        setWrapStyleWord(true);
        setOpaque(true);
    }

  @Override
    public Component getTableCellRendererComponent(JTable table, Object obj, boolean isSelected, boolean hasFocus, int row, int column) {
        if (isSelected) {
            setForeground(table.getSelectionForeground());
            setBackground(table.getSelectionBackground());
        } else {
            setForeground(table.getForeground());
            setBackground(table.getBackground());
        }

        setText((obj == null) ? "" : obj.toString());

        Rectangle rect = table.getCellRect(row, column, true);
        this.setSize(rect.getSize());

        int height = (int) getPreferredSize().getHeight();

        if (table != null && table.getRowHeight(row) < height) {
            table.setRowHeight(row, height);
        }

        setFont(table.getFont());

        return this;
    }
}
