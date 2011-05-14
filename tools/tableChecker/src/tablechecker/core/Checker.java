package tablechecker.core;

import java.io.File;
import java.io.FileFilter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import tablechecker.core.parsers.HtmlParser;
import tablechecker.core.parsers.WriterParser;

public class Checker {

  private File fileToCheck = null;
  private File templatesDir = null;

  public enum CheckResult {

    EQUAL, NONEQUAL
  }

  private boolean hasExtension(String fileName, String ext) {
    Pattern p = Pattern.compile(".{1,}\\." + ext + "$");
    Matcher m = p.matcher(fileName);
    boolean r = m.matches();
    return r;
  }

  private Table fileToTable(File f) {
    Table result = null;
    if (hasExtension(f.getName(), "html")) {
      try {
        HtmlParser parser = new HtmlParser(f.getCanonicalPath());
        result = parser.parse();
      } catch (IOException ex) {
        ex.printStackTrace(System.out);
      }
    } else if (hasExtension(f.getName(), "odt")) {
      try {
        WriterParser parser = new WriterParser(f.getCanonicalPath());
        result = parser.parse();
      } catch (IOException ex) {
        ex.printStackTrace(System.out);
      }
    }
    return result;
  }

  private CheckResult compare(Table t, Table template) {
    CheckResult result = CheckResult.EQUAL;

    ArrayList<Cell> templateC = template.getCells();
    for (Cell c : templateC) {
      if (!t.findCell(c)) {
        return CheckResult.NONEQUAL;
      }
    }

    return result;
  }

  public void check() {
    Table tableToTest = fileToTable(fileToCheck);

    if (tableToTest != null) {
      File[] files = templatesDir.listFiles(new FF());
      Arrays.sort(files);
      for (File f : files) {
        Table t = fileToTable(f);
        if (t != null) {
          CheckResult r = compare(tableToTest, t);
          if (r == CheckResult.EQUAL) {
            System.out.println("Таблица совпала с шаблоном " + f.getName());
          } else {
            System.out.println("Таблица не совпала с шаблоном " + f.getName());
          }
        }
      }
    }
  }

  public Checker(File fileToCheck, File templatesDir) {
    this.fileToCheck = fileToCheck;
    this.templatesDir = templatesDir;
  }

  private class FF
          implements FileFilter {

    @Override
    public boolean accept(File f) {
      boolean result = true;
      result &= f.isFile();
      return result;
    }
  }
}
