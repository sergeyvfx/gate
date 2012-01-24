package filteremail;

public class Main {

  public static void main(String[] args) {
    String fileToCheck = "";
    String fileNew = "C:\\Users\\keeper\\Desktop\\Для рассылки\\test.txt";
    String fileOld = "C:\\Users\\keeper\\Desktop\\Для рассылки\\bel_filter.txt";
    if (!"".equals(fileToCheck))
        new MyClass().check(fileToCheck);
    if (!"".equals(fileNew) && !"".equals(fileOld))
        new MyClass().except(fileNew, fileOld);
    
    /*if (args.length == 1) {
      new MyClass().check(args[0]);
    } else if (args.length == 2) {
      new MyClass().except(args[0], args[1]);
    } else {
      System.out.println("Usage: java -jar filtermail.jar (fileToCheck | fileNew fileOld)");
    }*/
  }
}
