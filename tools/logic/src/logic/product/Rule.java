package logic.product;

import java.io.Serializable;
import java.util.ArrayList;

public class Rule implements Serializable
{

  private String name;
  private ArrayList<Pair> ifPart;
  private ArrayList<Pair> thenPart;
  private String explanation;

  public Rule(String name)
  {
    this.name = name;
  }

  public Rule()
  {
    ifPart = new ArrayList<Pair>();
    thenPart = new ArrayList<Pair>();
  }

  public String getName()
  {
    return name;
  }

  public void setName(String name)
  {
    this.name = name;
  }

  public ArrayList<Pair> getIfPart()
  {
    return ifPart;
  }

  public ArrayList<Pair> getThenPart()
  {
    return thenPart;
  }

  public String getText()
  {
    String s = "IF ";
    for (Pair p : ifPart)
    {
      String tmp = "('" + p.getFrame().getName() + "." + p.getSlot().getName()
              + "' = '" + p.getValue().getValue() + "') and ";
      s = s.concat(tmp);
    }
    StringBuffer sb = new StringBuffer(s);
    sb.delete(s.length() - 4, s.length());
    s = sb.toString();
    s = s.concat("THEN ");
    for (Pair p : thenPart)
    {
      String tmp = "('" + p.getFrame().getName() + "." + p.getSlot().getName()
              + "' = '" + p.getValue().getValue() + "') and ";
      s = s.concat(tmp);
    }
    sb = new StringBuffer(s);
    sb.delete(s.length() - 4, s.length());
    s = sb.toString();
    return s;
  }

  public String getExplanation()
  {
    return explanation;
  }

  public void setExplanation(String explanation)
  {
    this.explanation = explanation;
  }

  public void setThenPart(ArrayList<Pair> thenPart)
  {
    this.thenPart = thenPart;
  }

  public void setIfPart(ArrayList<Pair> ifPart)
  {
    this.ifPart = ifPart;
  }
}
