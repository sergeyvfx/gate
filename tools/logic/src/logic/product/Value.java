package logic.product;

import java.io.Serializable;

public class Value implements Serializable
{

  private String value;
  private Domen domen;
  private int index;

  public Value(String value)
  {
    this.value = value;
  }

  public void setValue(String value)
  {
    this.value = value;
  }

  public String getValue()
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

  @Override
  public String toString()
  {
    return value;
  }
}
