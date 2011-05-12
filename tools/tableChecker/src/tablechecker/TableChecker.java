package tablechecker;

import java.io.File;
import java.io.IOException;
import tablechecker.core.Checker;
import tablechecker.core.libreoffice.LibreOffice;

public class TableChecker {

  private void check() {
  }

  public static void main(String[] args) {
    String jarFile = TableChecker.class.getProtectionDomain().
            getCodeSource().getLocation().getPath();
    if (args.length != 2) {
      System.out.println(
              "Usage: java -jar " + jarFile + " fileToCheck dirWithTemplates");
      return;
    }

    File fileToCheck = new File(args[0]);
    if (!fileToCheck.exists()) {
      System.out.println("File " + fileToCheck.getPath() + " not exists!");
      return;
    }
    if (!fileToCheck.isFile()) {
      System.out.println(fileToCheck.getPath() + " is not a file!");
      return;
    }
    if (!fileToCheck.canRead()) {
      System.out.println("File " + fileToCheck.getPath() + " can not be read!");
      return;
    }

    File templatesDir = new File(args[1]);
    if (!templatesDir.exists()) {
      System.out.println("Directory " + templatesDir.getPath() + " not exists!");
      return;
    }
    if (!templatesDir.isDirectory()) {
      System.out.println(templatesDir.getPath() + " is not a file!");
      return;
    }
    if (!templatesDir.canRead()) {
      System.out.println(
              "Directory " + templatesDir.getPath() + " can not be read!");
      return;
    }

    Checker checker = new Checker(fileToCheck, templatesDir);
    checker.check();
  }
}
