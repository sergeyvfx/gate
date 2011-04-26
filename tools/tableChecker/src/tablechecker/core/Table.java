package tablechecker.core;

import java.util.ArrayList;

public class Table {

  private String number; // Номер таблицы
  private String title; // Заголовок таблицы
  private ArrayList<Cell> cells; // Все ячейки таблицы
  private ArrayList<ArrayList<Cell>> rows; // Строки таблицы
  private ArrayList<ArrayList<Cell>> columns; // Столбцы таблицы

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

  public ArrayList<ArrayList<Cell>> getColumns() {
    return columns;
  }
  
  public void addCell(Cell c) {
    // TODO Проверить существует ли такая ячейка в таблице уже
    cells.add(c);
    // TODO Добавить ячейки в соответствующую строку и столбец
  }
}
