package tablechecker.frames.logic;

import java.io.Serializable;

public class Variable implements Serializable
{

  public static final int DERIVABLE_TYPE = 0;
  public static final int REQUERED_TYPE = 1;
  public static final int DERIVABLE_REQUERED_TYPE = 2;
  public static final String[] typeNames =
  {
    "Выводимая", "Запрашиваемая", "Выводимо-запрашиваемая"
  };
  private String name;
  private Value value;
  private Domen domen;
  private int type;
  private String question;

  public Variable(String name, Domen domen, int type, String question)
  {
    this.name = name;
    this.domen = domen;
    this.type = type;
    this.question = question;
  }

  public void setName(String name)
  {
    this.name = name;
  }

  public String getName()
  {
    return name;
  }

  public void setValue(Value value)
  {
    this.value = value;
  }

  public Value getValue()
  {
    return value;
  }

  public void setDomen(Domen domen)
  {
    this.domen = domen;
  }

  public Domen getDomen()
  {
    return domen;
  }

  public String getTypeName()
  {
    return typeNames[type];
  }

  public int getType()
  {
    return type;
  }

  public void setQuestion(String question)
  {
    this.question = question;
  }

  public String getQuestion()
  {
    return question;
  }

  public void setType(int type)
  {
    this.type = type;
  }

  public static int getTypeByName(String name)
  {
    for (int i = 0; i < typeNames.length; i++)
    {
      if (typeNames[i].equals(name))
      {
        return i;
      }
    }
    return 0;
  }

  @Override
  public String toString()
  {
    return getName();
  }
}
