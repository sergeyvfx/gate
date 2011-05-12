package tablechecker.core;

import java.util.ArrayList;
import java.util.List;

public class Table {

  private String number; // Номер таблицы
  private String title; // Заголовок таблицы
  private ArrayList<Cell> cells = new ArrayList<Cell>(); // Все ячейки таблицы
  private ArrayList<ArrayList<Cell>> rows = new ArrayList<ArrayList<Cell>>(); // Строки таблицы
  private ArrayList<ArrayList<Cell>> columns = new ArrayList<ArrayList<Cell>>(); // Столбцы таблицы

  public Table() {
  }

  public Table(String number, String title) {
    this.number = number;
    this.title = title;
  }

  public String getNumber() {
    return number;
  }

  public void setNumber(String number) {
    this.number = number;
  }

  public String getTitle() {
    return title;
  }

  public void setTitle(String title) {
    this.title = title;
  }

  public ArrayList<Cell> getCells() {
    return cells;
  }

  public ArrayList<ArrayList<Cell>> getRows() {
    return rows;
  }

  public int getRowCount() {
    return rows.size();
  }

  public ArrayList<ArrayList<Cell>> getColumns() {
    return columns;
  }

  public int getColumnCount() {
    return columns.size();
  }

  public Cell getCell(int row, int col) {
    for (Cell c : cells) {
      if (c.getRow() == row && c.getColumn() == col) {
        return c;
      }
    }
    return null;
  }

  public boolean isCell(int row, int col) {
    return getCell(row, col) != null;
  }

  public void addCell(Cell c) {
    int row = c.getRow();
    int col = c.getColumn();
    if (isCell(row, col)) {
      //TODO Сгенерировать исключение: такая ячейка уже существует.
      return;
    }
    cells.add(c);
    if (rows.size() - 1 < row) {
      rows.add(row, new ArrayList<Cell>());
    }
    rows.get(row).add(c);
    if (columns.size() - 1 < col) {
      columns.add(col, new ArrayList<Cell>());
    }
    columns.get(col).add(c);
  }

  public void addCells(List<Cell> cells) {
    for (Cell c : cells) {
      addCell(c);
    }
  }

  public ArrayList<ArrayList<Cell>> getHead() {
    ArrayList<ArrayList<Cell>> result = new ArrayList<ArrayList<Cell>>();

    for (ArrayList<Cell> cs : rows) {
      int s = result.size();
      if (cs.size() > 0) {
        Cell c = cs.get(0);
        if (c.getType() == Cell.Type.HEAD) {
          result.add(cs);
        }
      }
      if (s == result.size()) {
        break;
      }
    }

    return result;
  }

  public ArrayList<ArrayList<Cell>> getStud() {
    ArrayList<ArrayList<Cell>> result = new ArrayList<ArrayList<Cell>>();

    for (ArrayList<Cell> cs : columns) {
      int s = result.size();
      if (cs.size() > 0) {
        Cell c = cs.get(cs.size() - 1);
        if (c.getType() == Cell.Type.STUD) {
          result.add(cs);
        }
      }
      if (s == result.size()) {
        break;
      }
    }

    return result;
  }
}
