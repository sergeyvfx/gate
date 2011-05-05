package tablechecker;

import java.io.IOException;
import tablechecker.core.Parser;

public class TableChecker {

  public static void main(String[] args) {
    Parser p = new Parser("1.html");
    try {
      p.parse();
    } catch (IOException ex) {
      ex.printStackTrace();
    }
  }
}
