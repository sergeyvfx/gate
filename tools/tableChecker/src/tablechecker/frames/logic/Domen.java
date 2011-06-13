package tablechecker.frames.logic;

import java.io.Serializable;
import java.util.ArrayList;
import tablechecker.frames.Frame;
import tablechecker.frames.Frameset;
import tablechecker.frames.Slot;

public class Domen implements Serializable
{

  private String name;
  private ArrayList<Value> values;
  private Value v = new Value("Неизвестно");

  public Domen(String name)
  {
    this.name = name;
    this.values = new ArrayList<Value>();
    v.setDomen(this);
    values.add(v);
  }

  public String getName()
  {
    return name;
  }

  public void setName(String name)
  {
    this.name = name;
  }

  public void addValue(Value value)
  {
    value.setDomen(this);
    values.add(value);
  }

  public ArrayList<Value> getValues()
  {
    return values;
  }

  public boolean isValue(Value testVal, String s)
  {
    boolean res = true;
    for (Value value : values)
    {
      res = res && (!value.getValue().equalsIgnoreCase(s) || value == testVal);
    }
    return !res;
  }

  /**
   * Удаляет значение из домена по индексу
   * Внимание: не производится проверка на корректность удаления
   * @param index индекс значения для удаления
   */
  public void removeValue(int index)
  {
    values.remove(index);
  }

  @Override
  public String toString()
  {
    return name;
  }

  public Value getValueByName(String value)
  {
    for (Value v : values)
    {
      if (v.getValue().equals(value))
      {
        return v;
      }
    }
    return null;
  }

  private boolean canRemoveValue(Value value)
  {
    // Нельзя позволять удалять значение "Неизвестно"
    if (value.equals(v))
    {
      return false;
    }
    ArrayList<Frame> frames = Frameset.getInstance().getAllFrames();
    for (Frame f : frames)
    {
      ArrayList<Slot> slots = f.getOwnSlots();
      for (Slot s : slots)
      {
        if (s.getType() == Slot.ENUM && s.getValue().equals(value))
        {
          return false;
        }
      }
    }
    return true;
  }

  public boolean removeValue(Value value)
  {
    if (canRemoveValue(value))
    {
      return values.remove(value);
    }
    return false;
  }

  public void insertValue(int row, Value value)
  {
    values.add(row, value);
  }
}
