package tablechecker.core;

import java.io.File;
import java.io.FileFilter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.HashMap;
import java.util.Iterator;
import tablechecker.core.parsers.HtmlParser;
import tablechecker.core.parsers.WriterParser;
import logic.frames.Frame;
import logic.frames.Frameset;
import logic.frames.ISlot;
import logic.frames.Slot;
import logic.frames.Link;

public class Checker {

  private File fileToCheck = null;
  private File templatesDir = null;
  private Table duplicate;
  private ArrayList<Cell> bindCells = new ArrayList<Cell>();
  
  protected HashMap<String, String> pathToMethod;
  /*пути в таблице фреймов соответствует метод, который надо вызвать у объекта 
   * (который был вычислен ранее), чтобы получить данное свойство*/
  
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

    ArrayList<ArrayList<Cell>> head = template.getHead();

    for (ArrayList<Cell> r : head) {
      for (Cell c : r) {
        if (c.getTopLeftCell() == null) {
          //Ячейка является левой верхней в объединении
          String data = c.getData();
          Cell tc = t.findCell(data, c.getTier(), c.getType());
          if (tc != null) {
            //Ячейка с такими же данными на таком же уровне и такого же типа
            //нашлась
          }
        }
      }
    }

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
      /*File[] files = templatesDir.listFiles(new FF());
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
      }*/
      run(tableToTest);
    }
  }

  public Checker(File fileToCheck, File templatesDir) {
    this.fileToCheck = fileToCheck;
    this.templatesDir = templatesDir;
  }
  

  //Compare methods 
  
  public void run(Table data) {
    Frame rootFrame = getRootFrame();
    ArrayList<String> path = new ArrayList<String>();
    path.add("Таблица");

    if (rootFrame == null) /* notfing to do */ {
      return;
    }
    duplicate = data;
    bindCells.clear();
    bindFrame(rootFrame, data, path);
  }
  
  private Frame getRootFrame()
  {
    Frameset frameset = Frameset.getInstance();
    return frameset.getFrameByName("Таблица");
  }
  
  private int bindFrame(Frame frame, Object object, ArrayList<String> path) 
  {
    int result=-1;
    ArrayList<ISlot> slots = frame.getSlots();
    String spath = getPath(path);
    boolean ok = true;

    for (ISlot slot : slots) {
      ArrayList<String> newPath = path;
      newPath.add(slot.getName());
      
      Object newObject = object;
      if (object!=null && pathToMethod.get(getPath(newPath))!=null)
      {
        String method = pathToMethod.get(getPath(newPath));
        int k = method.indexOf("Типы(");
        String methodName="";
        ArrayList<String> paramTypes = new ArrayList<String>();
        ArrayList<String> params = new ArrayList<String>();
        if (k==-1)
          methodName = method.trim();
        else
        {
          methodName = method.substring(0, k-1).trim();
          method = method.substring(k+5);
          
          int posBracket = method.indexOf(")");
          int posSemi = method.indexOf(",");
          while (posSemi>=0) {            
            paramTypes.add(method.substring(0, posSemi-1).trim());
            method = method.substring(posSemi+1);
          }
          paramTypes.add(method.substring(0, posBracket-1).trim());
          method = method.substring(posBracket+1);
          
          k = method.indexOf("Параметры(");
          method = method.substring(k+10);
          
          posBracket = method.indexOf(")");
          posSemi = method.indexOf(",");
          while (posSemi>=0) {            
            params.add(method.substring(0, posSemi-1).trim());
            method = method.substring(posSemi+1);
          }
          params.add(method.substring(0, posBracket-1).trim());
        }
        
        Class[] parameterTypes = new Class[paramTypes.size()];
        int i = 0;
        for (String str : paramTypes) {
          if (str=="integer")
            parameterTypes[i] = int.class;
          else if (str == "string")
            parameterTypes[i] = String.class;
          else if (str == "boolean")
            parameterTypes[i] = boolean.class;
            
          i++;
        }
        
        Object[] parameters = new Class[params.size()];
        i = 0;
        for (String str : params) {
          parameters[i] = str;
        }
        
        try
        {
          newObject = object.getClass().getMethod(methodName, parameterTypes).invoke(object, params.toArray());
        }
        catch (Exception ex)
        {
          //throw new Exception(ex.getMessage());
        }
      }
      
      int tmp =  bindSlot(slot, newObject, newPath);
      if (tmp>result)
        result = tmp;
      if (tmp==-1)
        ok = false;
      if (tmp>0)
      {
        //Надо пробежаться по всем значениям, найти ссылки на этот слот и заменить эту ссылку на значение
        Collection<String> col = pathToMethod.values();
        int j = 0;
        for (String string : pathToMethod.values()) {
          if (string.contains(getPath(newPath)))
            pathToMethod.put(pathToMethod.keySet().toArray()[j].toString(), string.replace(getPath(newPath), String.valueOf(tmp)));
          j++;
        }
      }
    }
    return result;
  }
  
  private int bindSlot(ISlot slot, Object object, ArrayList<String> path) {
    int result = 0;

    //path.add(slot.getName());

    switch (slot.getType()) {
      case Slot.ENUM:
        result = bindEnumSlot(slot, object, path);
        break;
      case Slot.SUBFRAME:
        result = bindSubframeSlot(slot, object, path);
        break;
      /*case Slot.PRODUCTIONAL:
        result = bindProductionalSlot(slot, property, path);
        break;*/
    }

    return result;
  }
  
  private int bindEnumSlot(ISlot slot, Object object, ArrayList<String> path) {

    String slotVal = slot.getValue().getValue();

    /*if (val == null) {
      return slotVal == null || slotVal.equals("Неважно") || slotVal.equals("Неизвестно");
    }

    if (slotVal == null) {
      return val.equals("Неважно") || val.equals("Неизвестно");
    }
    
    if (val.equals("Неважно") || slotVal.equals("Неважно")) {
      return true;
    }*/
    if (object.getClass().equals(String.class))
      if (slotVal.equals(object.toString()))
        return 0;
    else if (object.getClass().isArray())
    {
      Object[] objs = (Object[]) object;
      int i=1;
      for (Object obj : objs) {
        if (slotVal.equals(obj.toString()))
          return i;
        i++;
      }
    }
    return -1;
  }

  private int bindSubframeSlot(ISlot slot, Object object, ArrayList<String> path) {
    Link link = slot.getInLink();

    if (link == null || link.getSource() == null) /* Assume void-linked subframe was binded */ {
      return 0;
    }

    Frame frame = link.getSource();

    return bindFrame(frame, object, path);
  }
  
  private String getPath(ArrayList<String> path)
  {
    String result = "";

    for (String x : path)
    {
      if (!result.equals(""))
        result += ".";
      result += x;
    }

    return result;
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
