package tablechecker;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import tablechecker.core.Checker;
import tablechecker.frames.Frame;
import tablechecker.frames.Frameset;

public class TableChecker {

  private void check() {
  }

  public static void main(String[] args) {
    String jarFile = TableChecker.class.getProtectionDomain().
            getCodeSource().getLocation().getPath();
    if (args.length != 2) {
      System.out.println(
              "Usage: java -jar " + jarFile + " fileToCheck fileWithFrames");
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

    Checker checker = new Checker(fileToCheck, fileWithFrames);
    checker.check();
  }
}
