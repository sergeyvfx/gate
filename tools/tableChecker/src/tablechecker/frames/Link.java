package tablechecker.frames;

import java.io.Serializable;

public class Link implements Serializable
{

  protected Frame source;
  protected Object target;
  protected int type;
  public static final int IS_A = 0, SUB_FRAME = 1, A_PART_OF = 2;

  public Link(Frame source, Object target, int type)
  {
    this.source = source;
    this.target = target;
    this.type = type;
  }

  public Frame getSource()
  {
    return source;
  }

  public Object getTarget()
  {
    return target;
  }

  public int getType()
  {
    return type;
  }

  public void setSource(Frame source)
  {
    this.source = source;
  }

  public void setTarget(Object target)
  {
    this.target = target;
  }

  public void setType(int type)
  {
    this.type = type;
  }

  @Override
  public String toString()
  {
    String res = "";
    switch (type)
    {
      case IS_A:
        res = "is a";
        break;
      case A_PART_OF:
        res = "a part of";
        break;
      case SUB_FRAME:
        res = "sub frame";
        break;
    }
    return res;
  }
}
