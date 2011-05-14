package tablechecker.core.parsers;

import java.util.ArrayList;
import java.util.List;
import tablechecker.core.Cell;
import tablechecker.core.Cell.Type;
import tablechecker.core.Table;

public class Formater {

  private int columnCount(List<Cell> row) {
    int result = 0;
    for (Cell c : row) {
      if (c.getTopLeftCell() == null
              || c.getTopLeftCell().getSpanColumn() == 1) {
        result++;
      }
    }
    return result;
  }

  private int rowCount(List<Cell> col) {
    int result = 0;
    for (Cell c : col) {
      if (c.getType() != Type.HEAD
              && (c.getTopLeftCell() == null
              || c.getTopLeftCell().getSpanRow() == 1)) {
        result++;
      }
    }
    return result;
  }

  protected void formatTable(Table table) {
    //Выделяем головку
    int headSize = 0;
    for (ArrayList<Cell> row : table.getRows()) {
      int colCount = columnCount(row);
      if (colCount <= table.getColumnCount()) {
        headSize++;
        for (Cell c : row) {
          c.setType(Type.HEAD);
        }
        if (colCount == table.getColumnCount()) {
          break;
        }
      }
    }

    //Выделяем боковик
    int rowCountWithOutHead = table.getRowCount() - headSize;
    for (ArrayList<Cell> col : table.getColumns()) {
      int rowCount = rowCount(col);
      if (rowCount <= rowCountWithOutHead) {
        for (Cell c : col) {
          if (c.getType() != Type.HEAD) {
            c.setType(Type.STUD);
          }
        }
        if (rowCount == rowCountWithOutHead) {
          break;
        }
      }
    }

    //Все остальное DATA
    for (Cell c : table.getCells()) {
      if (c.getType() == null) {
        c.setType(Type.DATA);
      }
    }

    //Выделяем уровни в головке
    int tier = 0;
    for (ArrayList<Cell> row : table.getHead()) {
      for (Cell c : row) {
        if (c.getTopLeftCell() == null) {
          c.setTier(tier);
        }
      }
      tier++;
    }

    //Выделяем уровни в боковике
    tier = 0;
    for (ArrayList<Cell> col : table.getStud()) {
      for (Cell c : col) {
        if (c.getTopLeftCell() == null && c.getType() == Cell.Type.STUD) {
          c.setTier(tier);
        }
      }
      tier++;
    }
  }
}
