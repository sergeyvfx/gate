package tablechecker.core;

import java.io.File;
import java.io.FileFilter;
import java.io.IOException;
import java.lang.reflect.Array;
import java.util.ArrayList;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.HashMap;
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
  private int resultMark = 0;
  private int allMarks = 0;
  private ArrayList<Frame> doneFrames = new ArrayList<Frame>();
  
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
      pathToMethod = new HashMap<String, String>();
      pathToMethod.put("Таблица.Заголовок таблицы", "getTitle");
      pathToMethod.put("Таблица.Табличный номер", "getNumber");
      pathToMethod.put("Таблица.Головка.Заголовок боковика", "getHeadOfStud");
      pathToMethod.put("Таблица.Головка.Заголовок боковика.Текст", "getData");
      pathToMethod.put("Таблица.Головка.Свойства", "getPropertySetOfHead");
      pathToMethod.put("Свойство.Название", "getData");
      pathToMethod.put("Таблица.Боковик.Объекты", "getObjectSetOfStud");
      pathToMethod.put("Объект.Название", "getData");
      pathToMethod.put("Значение", "getCell Типы(String,String) Параметры(Объект.Название.Текст, Свойство.Название.Текст)");
      pathToMethod.put("Значение.Значение", "getData");
            
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
    resultMark = 0;
    allMarks = 0;
    doneFrames.clear();
    CountResult(rootFrame);
    
    System.out.print("Набрано " + resultMark + " баллов из "+allMarks+"\n");
  }
  
  private Frame getRootFrame()
  {
    Frameset frameset = Frameset.getInstance();
    return frameset.getFrameByName("Таблица");
  }
  
  private boolean bindFrame(Frame frame, Object object, ArrayList<String> path) 
  {
    System.out.print("Привязываем фрейм "+frame.getName()+"\n");
    ISlot islot = frame.getSlotByName("#Used");
    if (islot !=null && "Да".equals(islot.getValue().getValue()))
    {
      System.out.print("Фрейм "+frame.getName()+" не привязался\n");
      return false;
    }
    ArrayList<ISlot> slots = frame.getSlots();
    String spath = getPath(path);
    boolean ok = true;

    for (ISlot slot : slots) {
      String slotName = slot.getName();
      if (!slotName.startsWith("#"))
      {
        ArrayList<String> newPath = new ArrayList<String>(path);
        newPath.add(slotName);
      
        Object newObject = GetNewObject(object, getPath(newPath), frame);
        System.out.print("Привязываем слот "+slot.getName()+"\n");
        if (!bindSlot(slot, newObject, newPath))
        {
          System.out.print("cлот "+slot.getName()+" не привязался\n");
          ok = false;
        }
        else
          System.out.print("cлот "+slot.getName()+" привязался\n");
      }
    }
    if (ok)
    {
      if (frame.getSlotByName("#Вес")!=null && frame.getSlotByName("#Баллы")!=null)
      {
        frame.getSlotByName("#Баллы").setText(frame.getSlotByName("#Вес").getText());
        System.out.print(getPath(path)+": " +frame.getSlotByName("#Баллы").getText()+"\n");
      }
      if (frame.getSlotByName("#Used")!=null)
        frame.getSlotByName("#Used").setValue(frame.getSlotByName("#Used").getValue().getDomen().getValueByName("Да"));
      
      System.out.print("фрейм "+frame.getName()+" привязался\n");
    }
    else
      System.out.print("фрейм "+frame.getName()+" не привязался\n");
    return ok;
  }
  
  private Object GetNewObject(Object object, String path, Frame frame)
  {
    Object newObject = object;
        if (object!=null && pathToMethod.containsKey(path))
        {
          String method = pathToMethod.get(path);
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
            while (posSemi>=0 && posBracket>=0 && posSemi<posBracket) {            
              paramTypes.add(method.substring(0, posSemi).trim());
              method = method.substring(posSemi+1);
              posBracket = method.indexOf(")");
              posSemi = method.indexOf(",");            
            }
            paramTypes.add(method.substring(0, posBracket).trim());
            method = method.substring(posBracket+1);
            
            k = method.indexOf("Параметры(");
            method = method.substring(k+10);
            
            posBracket = method.indexOf(")");
            posSemi = method.indexOf(",");
            while (posSemi>=0) {            
              String p = method.substring(0, posSemi).trim();
              params.add(TryToFindValue(frame, p));
              method = method.substring(posSemi+1);
              posSemi = method.indexOf(",");
            }
            posBracket = method.indexOf(")");
            params.add(TryToFindValue(frame, method.substring(0, posBracket).trim()));
          }
          
          Class[] parameterTypes = new Class[paramTypes.size()];
          int i = 0;
          for (String str : paramTypes) {
            if ("Integer".equals(str))
              parameterTypes[i] = int.class;
            else if ("String".equals(str))
              parameterTypes[i] = String.class;
            else if ("boolean".equals(str))
              parameterTypes[i] = boolean.class;
            
            i++;
          }
        
          Object[] parameters = new Object[params.size()];
          i = 0;
          for (String str : params) {
            parameters[i] = (Object)str;
          }
        
          try
          {
            newObject = object.getClass().getMethod(methodName, parameterTypes).invoke(object, params.toArray());
          }
          catch (Exception ex)
          { 
            try
            {
            Array.getLength(object);
            Object[] objs = (Object[]) object;
            ArrayList<Object> tmpObjs = new ArrayList<Object>();
            for (Object object1 : objs) {
              tmpObjs.add(object1.getClass().getMethod(methodName, parameterTypes).invoke(object1, params.toArray()));
            }
            newObject = tmpObjs.toArray();
            }
            catch (Exception exept)
            {
            }
            
            //throw new Exception(ex.getMessage());
          }
        }
    return newObject;
  }
  
  private boolean bindSlot(ISlot slot, Object object, ArrayList<String> path) {
    boolean result = true;

    //path.add(slot.getName());

    switch (slot.getType()) {
      /*case Slot.ENUM:
        result = bindEnumSlot(slot, object, path);
        break;*/
      case Slot.SUBFRAME:
        result = bindSubframeSlot(slot, object, path);
        result = true;
        break;
      case Slot.TEXT:
        result = bindTextSlot(slot, object, path);
        break;
      /*case Slot.PRODUCTIONAL:
        result = bindProductionalSlot(slot, property, path);
        break;*/
    }

    return result;
  }
  
  private boolean bindEnumSlot(ISlot slot, Object object, ArrayList<String> path) {

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
        return true;
    else if (object.getClass().isArray())
    {
      Object[] objs = (Object[]) object;
      for (Object obj : objs) {
        if (slotVal.equals(obj.toString()))
          return true;
      }
    }
    return false;
  }
  
  private boolean bindTextSlot(ISlot slot, Object object, ArrayList<String> path) {

    String slotVal = slot.getText();
    String val = object!=null?object.toString():null;
    if (val == null) {
      return slotVal == null || slotVal.equals("Неважно") || slotVal.equals("Неизвестно");
    }

    if (slotVal == null) {
      //return (val.equals("Неважно") || val.equals("Неизвестно"))?0:-1;
      return false;
    }
    
    /*if (val.equals("Неважно") || slotVal.equals("Неважно")) {
      return true;
    }*/
    
    try
    {
      Array.getLength(object);
      Object[] objs = (Object[]) object;
      for (Object obj : objs) {
        if (slotVal.equals(obj.toString()))
          return true;
      }
    }
    catch (IllegalArgumentException ex)
    {
      if (object.getClass().equals(String.class))
        if (slotVal.equals(object.toString()))
          return true;
    }
    return false;
  }


  private boolean bindSubframeSlot(ISlot slot, Object object, ArrayList<String> path) {
    Link link = slot.getInLink();

    if (link == null || link.getSource() == null) /* Assume void-linked subframe was binded */ {
      return true;
    }

    Frame frame = link.getSource();
    
    if (frame.getInLinks().isEmpty())
    {
      return bindFrame(frame, object, path);
    }
    else
    {
      boolean ok = false;
      ArrayList<Link> links = frame.getInLinks();
      for (Link l : links) {
        Frame fr = l.getSource();
        ArrayList<String> newPath = new ArrayList<String>();
        newPath.add(frame.getName());
        Object newObject = GetNewObject(object, getPath(newPath), fr);
        ok = bindFrame(fr, newObject, newPath);
        if (ok)
          break;
      }
      return ok;
    }
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
  
  private String TryToFindValue(Frame frame, String string)
  {
    String[] path = string. split("\\.");
    for (String string1 : path) {
      ISlot slot = frame.getSlotByName(string1);
      if (slot!=null)
      {
        switch (slot.getType())
        {
          case Slot.SUBFRAME:
            frame = slot.getInLink().getSource();
            break;
          case Slot.TEXT:
            return slot.getText();
          case Slot.ENUM:
            return slot.getValue().getValue();
          default:
            return string;
        }
      }
      else
        return string;                
    }
    return string;    
  }
  
  
  private void CountResult(Frame frame)
  {
    if (doneFrames.contains(frame))
      return;
    if (frame.getInLinks().isEmpty())
      doneFrames.add(frame);
    ArrayList<ISlot> slots = frame.getSlots();
    for (ISlot iSlot : slots) {
      if ("#Вес".equals(iSlot.getName()))
      {
        if (iSlot.getText()!=null)
          allMarks += Integer.parseInt(iSlot.getText());
      }
      else if ("#Баллы".equals(iSlot.getName()))
      {
        if (iSlot.getText()!=null)
          resultMark += Integer.parseInt(iSlot.getText());
      }
      else if (iSlot.getType()==Slot.SUBFRAME)
      {
        Link link = iSlot.getInLink();

        if (link != null && link.getSource() != null)
          CountResult(link.getSource());      
      }
    }
    ArrayList<Link> links =  frame.getInLinks();
    for (Link link : links) {
      CountResult(link.getSource());
    }
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
