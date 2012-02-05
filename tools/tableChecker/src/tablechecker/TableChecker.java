package tablechecker;

import java.io.File;
import java.io.IOException;
import logic.frames.Frameset;
import tablechecker.core.Checker;

public class TableChecker {

  private void check() {
  }

  public static void main(String[] args) {
    String jarFile = TableChecker.class.getProtectionDomain().
            getCodeSource().getLocation().getPath();
    if (args.length != 2) {
      System.out.println(
              "Usage: java -jar " + jarFile + " fileName.(html|odt|ods|doc|xls) fileName.frs");
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

    File fileWithFrames = new File(args[1]);
    if (!fileWithFrames.exists()) {
      System.out.println("File " + fileWithFrames.getPath() + " not exists!");
      return;
    }
    if (fileWithFrames.isDirectory()) {
      System.out.println(fileWithFrames.getPath() + " is not a file!");
      return;
    }
    if (!fileWithFrames.canRead()) {
      System.out.println(
              "File " + fileWithFrames.getPath() + " can not be read!");
      return;
    }

    // Загружаем Frameset
    try {
      Frameset.getInstance().load(fileWithFrames);
    } catch (IOException ex) {
      ex.printStackTrace(System.err);
    }
    // Теперь надо запускать проверку
    Checker checker = new Checker(fileToCheck, fileWithFrames);
    checker.check();
  }
}
