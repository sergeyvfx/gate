/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package filteremail;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.ArrayList;
import java.util.Scanner;
import java.util.regex.Pattern;

/**
 *
 * @author alexanis
 */
public class MyClass {

  private Pattern p = Pattern.compile("^([a-zA-Z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}$");

  private ArrayList<String> readFile(String filename) {
    ArrayList<String> res = new ArrayList<String>();
    File f = new File(filename);
    try {
      Scanner scanner = new Scanner(f);
      while (scanner.hasNextLine()) {
        String line = scanner.nextLine();
        line = line.toLowerCase().trim();
        if (p.matcher(line).matches()) {
          res.add(line);
        } else {
          System.err.println("E-mail [" + line + "] не корректный!");
        }
      }
    } catch (FileNotFoundException ex) {
      System.err.println("Файл не найден!");
    }
    return res;
  }

  private void printArray(ArrayList<String> arr) {
    for (String s : arr) {
      System.out.println(s);
    }
  }

  public void check(String filePath) {
    ArrayList<String> emails = readFile(filePath);
    String s = "";
    int i = 0;
    while (i < emails.size()) {
      s = emails.get(i);
      boolean b = true;
      while (b) {
        int j = emails.lastIndexOf(s);
        if (j == i) {
          b = false;
        } else {
          emails.remove(j);
        }
      }
      i++;
    }
    printArray(emails);
  }

  public void except(String filePathNew, String filePathOld) {
    ArrayList<String> emails_new = readFile(filePathNew);
    ArrayList<String> emails_old = readFile(filePathOld);
    String s = "";
    int i = 0;
    while (i < emails_new.size()) {
      s = emails_new.get(i);
      if (emails_old.contains(s)) {
        emails_new.remove(i);
      } else {
        i++;
      }
    }
    printArray(emails_new);
  }
}
