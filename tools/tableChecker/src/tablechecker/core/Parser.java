package tablechecker.core;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import org.htmlcleaner.CleanerProperties;
import org.htmlcleaner.HtmlCleaner;
import org.htmlcleaner.TagNode;
import tablechecker.core.Cell.Type;

public class Parser {

  private String fileName;

  public Parser(String fileName) {
    this.fileName = fileName;
  }

  /**
   * Добавляет в строку таблицы ячейку, находящуюся в объединенной области
   * @param joinCells - список левых верних ячеек
   * @param cells - список всех ячеек в строке
   * @param rowCount - номер текущей строки
   * @param colCount - номер текущего столбца
   */
  private void addJoinCells(List<Cell> joinCells, List<Cell> cells, int rowCount, int[] colCount) {
    boolean flag = true;

    while (flag) {
      int oldColCount = colCount[0];

      for (Cell c : joinCells) {
        if (rowCount >= c.getRow()
                && rowCount < c.getRow() + c.getSpanRow()
                && colCount[0] >= c.getColumn()
                && colCount[0] < c.getColumn() + c.getSpanColumn()
                && (rowCount != c.getRow()
                || colCount[0] != c.getColumn())) {
          //Ячейка находится в объединенной области и не является левым верхним углом
          //Добавляем ее к строке, изменяем текущий столбец
          Cell inCell = new Cell(null, rowCount, colCount[0], 0, 0, c);
          cells.add(inCell);
          colCount[0]++;
          break;
        }
      }

      flag = !(oldColCount == colCount[0]);
    }
  }

  /**
   * Парсер строки
   * @param nodes - элементы строки
   * @param rowCount - номер строки
   * @param joinCells - список левых верхних ячеек
   * @return список ячеек в строке
   */
  private List<Cell> parseRow(List<TagNode> nodes, int rowCount, List<Cell> joinCells) {
    ArrayList<Cell> cells = new ArrayList<Cell>();
    int[] colCount = new int[1];

    addJoinCells(joinCells, cells, rowCount, colCount);

    while (nodes.size() > 0) {
      TagNode n = nodes.remove(0);
      if (n.getName().equals("td")) {
        String data = n.getText().toString();
        int spanRow = 1;
        int spanCol = 1;
        Cell.HAlignment ha = Cell.HAlignment.LEFT;
        Cell.VAlignment va = Cell.VAlignment.MIDDLE;
        try {
          spanRow = Integer.parseInt(n.getAttributeByName("rowspan"));
        } catch (NumberFormatException ex) {
        }
        try {
          spanCol = Integer.parseInt(n.getAttributeByName("colspan"));
        } catch (NumberFormatException ex) {
        }
        try {
          ha = Cell.HAlignment.valueOf(n.getAttributeByName("align").toUpperCase());
        } catch (Exception ex) {
        }
        try {
          va = Cell.VAlignment.valueOf(n.getAttributeByName("valign").toUpperCase());
        } catch (Exception ex) {
        }
        Cell c = new Cell(data, rowCount, colCount[0], spanRow, spanCol, null);
        c.setHAlignment(ha);
        c.setVAlignment(va);
        cells.add(c);
        colCount[0]++;

        if (spanCol > 1 || spanRow > 1) {
          joinCells.add(c);
        }

        addJoinCells(joinCells, cells, rowCount, colCount);
      }
    }
    return cells;
  }

  /**
   * Парсер таблицы
   * @param table - Таблица для заполнения
   * @param nodes - Список тегов для парсинга
   */
  private void parseTable(Table table, List<TagNode> nodes) {
    int rowCount = 0;
    List<Cell> joinCells = new ArrayList<Cell>();
    while (nodes.size() > 0) {
      TagNode n = nodes.remove(0);
      if (n.getName().equals("tr")) {
        List<Cell> cells = parseRow(n.getChildTagList(), rowCount, joinCells);
        table.addCells(cells);
        rowCount++;
      } else {
        nodes.addAll(0, n.getChildTagList());
      }
    }
  }

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

  private void formatTable(Table table) {
    //Выделяем головку
    for (ArrayList<Cell> row : table.getRows()) {
      int colCount = columnCount(row);
      if (colCount <= table.getColumnCount()) {
        for (Cell c : row) {
          c.setType(Type.HEAD);
        }
        if (colCount == table.getColumnCount()) {
          break;
        }
      }
    }
  }

  /**
   * Парсер html страницы
   * @return таблица с html страницы
   * @throws IOException
   */
  public Table parse() throws IOException {
    HtmlCleaner cleaner = new HtmlCleaner();
    CleanerProperties props = cleaner.getProperties();
    TagNode node = cleaner.clean(new File(fileName));
    Table table = new Table();

    //Помещаем все вершины для обхода в список
    List<TagNode> nodesToLook = node.getChildTagList();
    //Пока список не пуст
    while (nodesToLook.size() > 0) {
      TagNode n = nodesToLook.remove(0);
      System.out.println(n.getName());
      if (n.getName().equals("table")) {
        parseTable(table, n.getChildTagList());
        formatTable(table);
      }
      if (n.hasChildren()) {
        nodesToLook.addAll(0, n.getChildTagList());
      }
    }
    return table;
  }
}
