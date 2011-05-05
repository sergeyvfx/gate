package tablechecker.core;

public class Cell {

  public enum Type {

    HEAD, // Головка
    STUD, // Боковик
    DATA // Прографка
  };

  public enum HAlignment {

    LEFT, // По левому краю
    RIGHT, // По правому краю
    CENTER, // По центру
    JUSTIFY // По ширине
  };

  public enum VAlignment {

    TOP, // По верхнему краю
    BOTTOM, // По нижнему краю
    MIDDLE, // По середине
    BASELINE // Выравнивание по базовой линии
  };

  private String data; // Данные
  private HAlignment ha; // Тип выравнивание по горизотали
  private VAlignment va; // Тип выравнивания по вертикали
  private Type type; // Тип ячейки (Головка, Боковик, Прографка)
  private int row; // Номер строки в которой содержится ячейка
  private int column; // Номер столбца в котором содержится ячейка
  private int tier; // Ярус ячейки
  private int spanRow; // Протяженность по строкам
  private int spanColumn; // Протяженность по столбцам
  private Cell topLeftCell; // Левая верхняя ячейка, если данная ячейка находится в объединенной области

  public Cell() {
  }

  public Cell(String data, int row, int column, int spanRow, int spanColumn, Cell topLeftCell) {
    this.data = data;
    this.row = row;
    this.column = column;
    this.spanRow = spanRow;
    this.spanColumn = spanColumn;
    this.topLeftCell = topLeftCell;
  }

  public HAlignment getHAlignment() {
    return ha;
  }

  public void setHAlignment(HAlignment alignment) {
    this.ha = alignment;
  }

  public VAlignment getVAlignment() {
    return va;
  }

  public void setVAlignment(VAlignment alignment) {
    this.va = alignment;
  }

  public Type getType() {
    return type;
  }

  public void setType(Type type) {
    this.type = type;
  }

  public int getRow() {
    return row;
  }

  public void setRow(int row) {
    this.row = row;
  }

  public int getColumn() {
    return column;
  }

  public void setColumn(int column) {
    this.column = column;
  }

  public int getTier() {
    return tier;
  }

  public void setTier(int tier) {
    this.tier = tier;
  }

  public int getSpanRow() {
    return spanRow;
  }

  public void setSpanRow(int spanRow) {
    this.spanRow = spanRow;
  }

  public int getSpanColumn() {
    return spanColumn;
  }

  public void setSpanColumn(int spanColumn) {
    this.spanColumn = spanColumn;
  }

  public Cell getTopLeftCell() {
    return topLeftCell;
  }

  public void setTopLeftCell(Cell topLeftCell) {
    this.topLeftCell = topLeftCell;
  }
}
