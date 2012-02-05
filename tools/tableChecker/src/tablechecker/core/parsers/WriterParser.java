package tablechecker.core.parsers;

import com.sun.star.beans.PropertyValue;
import com.sun.star.beans.UnknownPropertyException;
import com.sun.star.beans.XPropertySet;
import com.sun.star.comp.helper.BootstrapException;
import com.sun.star.container.XEnumeration;
import com.sun.star.container.XEnumerationAccess;
import com.sun.star.container.XIndexAccess;
import com.sun.star.container.XNameAccess;
import com.sun.star.frame.FrameSearchFlag;
import com.sun.star.frame.XComponentLoader;
import com.sun.star.frame.XDesktop;
import com.sun.star.lang.WrappedTargetException;
import com.sun.star.lang.XComponent;
import com.sun.star.lang.XMultiComponentFactory;
import com.sun.star.lang.XServiceInfo;
import com.sun.star.style.ParagraphAdjust;
import com.sun.star.table.XCell;
import com.sun.star.table.XCellRange;
import com.sun.star.table.XTableColumns;
import com.sun.star.table.XTableRows;
import com.sun.star.text.TableColumnSeparator;
import com.sun.star.text.VertOrientation;
import com.sun.star.text.XText;
import com.sun.star.text.XTextDocument;
import com.sun.star.text.XTextTable;
import com.sun.star.text.XTextTablesSupplier;
import com.sun.star.uno.UnoRuntime;
import com.sun.star.uno.XComponentContext;
import com.sun.star.util.CloseVetoException;
import com.sun.star.util.XCloseable;
import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;
import tablechecker.core.Cell;
import tablechecker.core.Table;

public class WriterParser
        extends Formater
        implements Parser {

  private boolean connected = false;
  private XComponentContext xContext = null;
  private XMultiComponentFactory xMCF = null;
  private XDesktop xDesktop = null;
  private String fileName;

  public WriterParser(String fileName) {
    this.fileName = fileName;
  }

  @Override
  public Table parse() {
    Table resultTable = null;
    try {
      resultTable = new Table();
      connect();
      HashMap<String, Object> props = new HashMap<String, Object>();
      props.put("Hidden", true);
      XComponent xDoc = openDocument(fileName, props);
      XTextDocument xTextDocument = UnoRuntime.queryInterface(
              XTextDocument.class, xDoc);

      XTextTablesSupplier xtts = UnoRuntime.queryInterface(
              XTextTablesSupplier.class, xTextDocument);
      XNameAccess xNamedTables = xtts.getTextTables();
      XIndexAccess xIndexedTables = UnoRuntime.queryInterface(XIndexAccess.class,
              xNamedTables);

      if (xIndexedTables.getCount() > 0) {
        Object textTable = xIndexedTables.getByIndex(0);
        XTextTable xTextTable = UnoRuntime.queryInterface(XTextTable.class,
                textTable);
        XTableRows xTableRows = xTextTable.getRows();
        int rowCount = xTableRows.getCount();
        int sum = getTableColumnRelativeSum(xTextTable);
        int[] colCounts = new int[rowCount];

        ArrayList<ArrayList<Integer>> matrix = new ArrayList<ArrayList<Integer>>(
                rowCount);
        for (int i = 0; i < rowCount; i++) {
          matrix.add(new ArrayList<Integer>());
        }

        int maxColCount = 0;
        for (int rowNo = 0; rowNo < rowCount; rowNo++) {
          TableColumnSeparator[] sep = getTableRowSeparator(xTableRows, rowNo);
          colCounts[rowNo] = sep.length + 1;

          if (maxColCount < colCounts[rowNo]) {
            maxColCount = colCounts[rowNo];
          }

          for (int j = 0; j < sep.length; j++) {
            matrix.get(rowNo).add((int) sep[j].Position);
          }

          matrix.get(rowNo).add(sum);
        }

        int[] curIndex = new int[rowCount];

        ArrayList<ArrayList<TCell>> tcs = new ArrayList<ArrayList<TCell>>();
        for (int i = 0; i < rowCount; i++) {
          tcs.add(new ArrayList<TCell>());
        }

        int curMinSep = matrix.get(0).get(0);
        do {
          curMinSep = matrix.get(0).get(curIndex[0]);
          for (int i = 0; i < rowCount; i++) {
            if (curMinSep > matrix.get(i).get(curIndex[i])) {
              curMinSep = matrix.get(i).get(curIndex[i]);
            }
          }

          for (int rowNo = 0; rowNo < rowCount; rowNo++) {
            int col = curIndex[rowNo];
            int lastInd = tcs.get(rowNo).size() - 1;

            if (curMinSep == matrix.get(rowNo).get(col)) {
              if (colCounts[rowNo] > col + 1) {
                curIndex[rowNo] = col + 1;
              }
              if (tcs.get(rowNo).size() > 0
                      && tcs.get(rowNo).get(lastInd).text < 1
                      && tcs.get(rowNo).get(lastInd).colspan > 0) {
                tcs.get(rowNo).get(lastInd).colspan++;
                tcs.get(rowNo).get(lastInd).text++;
              } else {
                tcs.get(rowNo).add(new TCell(rowNo, col, 0, 0, 1));
              }
            } else {
              if (tcs.get(rowNo).size() > 0
                      && tcs.get(rowNo).get(lastInd).text < 1) {
                tcs.get(rowNo).get(lastInd).colspan++;
              } else {
                tcs.get(rowNo).add(new TCell(0, 1));
              }
            }
          }
        } while (curMinSep < sum);

        for (int i = 0; i < xTableRows.getCount(); i++) {
          XTableColumns xTableColumns = xTextTable.getColumns();
          for (int j = 0; j < xTableColumns.getCount(); j++) {
            XCellRange xCellRange = UnoRuntime.queryInterface(XCellRange.class,
                    textTable);
            XCell xCell = xCellRange.getCellByPosition(j, i);
            XPropertySet cellSet = UnoRuntime.queryInterface(XPropertySet.class,
                    xCell);
            XText xtc = UnoRuntime.queryInterface(XText.class, xCell);
            XEnumerationAccess xea = UnoRuntime.queryInterface(
                    XEnumerationAccess.class, xtc);
            XEnumeration xParaEnum = xea.createEnumeration();
            while (xParaEnum.hasMoreElements()) {
              XServiceInfo xsi = UnoRuntime.queryInterface(XServiceInfo.class,
                      xParaEnum.nextElement());
              if (!xsi.supportsService("com.sun.star.text.TextTable")) {
                XPropertySet set = UnoRuntime.queryInterface(XPropertySet.class,
                        xsi);
                int h = (Integer) set.getPropertyValue("ParaAdjust");
                Cell.HAlignment hAlignment = Cell.HAlignment.CENTER;
                switch (h) {
                  case ParagraphAdjust.CENTER_value:
                    hAlignment = Cell.HAlignment.CENTER;
                    break;
                  case ParagraphAdjust.LEFT_value:
                    hAlignment = Cell.HAlignment.LEFT;
                    break;
                  case ParagraphAdjust.RIGHT_value:
                    hAlignment = Cell.HAlignment.RIGHT;
                    break;
                  case ParagraphAdjust.BLOCK_value:
                    hAlignment = Cell.HAlignment.JUSTIFY;
                }

                int v = (Integer) cellSet.getPropertyValue("VertOrient");
                Cell.VAlignment vAlignment = Cell.VAlignment.TOP;
                switch (v) {
                  case VertOrientation.TOP:
                    vAlignment = Cell.VAlignment.TOP;
                    break;
                  case VertOrientation.BOTTOM:
                    vAlignment = Cell.VAlignment.BOTTOM;
                    break;
                  case VertOrientation.CENTER:
                    vAlignment = Cell.VAlignment.MIDDLE;
                    break;
                }

                int rowSpan = 1;
                int colSpan = 1;


                //Создаем ячейку
                Cell c = new Cell(xtc.getString(), i, j, rowSpan, colSpan, null);
                c.setHAlignment(hAlignment);
                c.setVAlignment(vAlignment);
                resultTable.addCell(c);

              }
            }
          }
        }
      }
      closeDocument(xDoc);
      terminate();
    } catch (Exception ex) {
      ex.printStackTrace(System.out);
    }
    formatTable(resultTable);
    return resultTable;
  }

  private int getTableColumnRelativeSum(XTextTable rows) {
    int result = 0;
    XPropertySet xPropertySet = UnoRuntime.queryInterface(XPropertySet.class,
            rows);
    try {
      result = ((Short) xPropertySet.getPropertyValue("TableColumnRelativeSum")).
              intValue();
    } catch (UnknownPropertyException ex) {
    } catch (WrappedTargetException ex) {
    }

    return result;
  }

  private TableColumnSeparator[] getTableRowSeparator(XTableRows rows, int n) {

    Object o = null;
    TableColumnSeparator[] sep = null;
    try {
      o = rows.getByIndex(n);
    } catch (com.sun.star.lang.IndexOutOfBoundsException ex) {
    } catch (WrappedTargetException ex) {
    }
    if (o != null) {
      XPropertySet xPropertySet = UnoRuntime.queryInterface(XPropertySet.class,
              o);
      try {
        sep = (TableColumnSeparator[]) xPropertySet.getPropertyValue(
                "TableColumnSeparators");
      } catch (UnknownPropertyException ex) {
      } catch (WrappedTargetException ex) {
      }
    }

    return sep;
  }

  private void connect() {
    if (isConnected()) {
      //TODO Генерировать исключение уже подключен
    }
    try {
      xContext = com.sun.star.comp.helper.Bootstrap.bootstrap();
      if (xContext != null) {
        xMCF = xContext.getServiceManager();
        if (xMCF != null) {
          Object o = xMCF.createInstanceWithContext("com.sun.star.frame.Desktop",
                  xContext);
          if (o != null) {
            xDesktop = UnoRuntime.queryInterface(XDesktop.class, o);
            if (xDesktop != null) {
//              xDesktop.getCurrentFrame().getContainerWindow().setVisible(false);
              connected = true;
            } else {
              //TODO Генерировать исключение "Couldn't query interface XDesktop"
            }
          } else {
            //TODO Генерировать исключение "Couldn't create instance with context 
            //com.sun.star.frame.Desktop"
          }
        } else {
          //TODO Генерировать исключение "Couldn't get service manager"
        }
      } else {
        //TODO Генерировать исключение "Couldn't get xComponentContent"
      }
    } catch (BootstrapException ex) {
      //TODO Прикрутить логирование
    } catch (com.sun.star.uno.Exception ex) {
    }
  }

  private boolean isConnected() {
    return connected;
  }

  private XComponent openDocument(String fileName)
          throws IOException, com.sun.star.io.IOException,
          com.sun.star.lang.IllegalArgumentException {
    return openDocument(fileName, new HashMap<String, Object>());
  }

  private XComponent openDocument(String fileName,
          Map<String, Object> properties)
          throws IOException, com.sun.star.io.IOException,
          com.sun.star.lang.IllegalArgumentException {
    PropertyValue[] arguments = new PropertyValue[properties.size()];
    int i = 0;
    for (Map.Entry<String, Object> e : properties.entrySet()) {
      arguments[i] = new PropertyValue();
      arguments[i].Name = e.getKey();
      arguments[i].Value = e.getValue();
    }
    String targetFrameName = "_blank";
    int frameSearchFlag = FrameSearchFlag.AUTO;
    String url = "file:///".concat((new File(fileName)).getCanonicalPath().
            replace("\\", "/"));
    return openDocument(url, targetFrameName, frameSearchFlag, arguments);
  }

  private XComponent openDocument(String url, String targetFrameName,
          int frameSearchFlag, PropertyValue[] arguments)
          throws com.sun.star.io.IOException,
          com.sun.star.lang.IllegalArgumentException {
    XComponent xDoc = null;
    if (!isConnected()) {
      //TODO Генерировать исключение "You should connect before openDocument"
    }
    XComponentLoader xCLoader = UnoRuntime.queryInterface(
            XComponentLoader.class, xDesktop);
    xDoc = xCLoader.loadComponentFromURL(url, targetFrameName,
            frameSearchFlag, arguments);
    return xDoc;
  }

  private void closeDocument(XComponent xDoc)
          throws CloseVetoException {
    XCloseable xc = UnoRuntime.queryInterface(XCloseable.class, xDoc);
    if (xc != null) {
      xc.close(false);
    } else {
      xDoc.dispose();
    }
  }

  private boolean terminate() {
    return xDesktop.terminate();
  }

  private class TCell {

    int row;
    int col;
    int rowspan;
    int colspan;
    int text;

    public TCell(int row, int col, int rowspan, int colspan, int text) {
      this.row = row;
      this.col = col;
      this.rowspan = rowspan;
      this.colspan = colspan;
      this.text = text;
    }

    public TCell(int rowspan, int colspan) {
      this.rowspan = rowspan;
      this.colspan = colspan;
    }
  }
}
