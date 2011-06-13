package tablechecker.frames.logic;

import java.io.Serializable;
import tablechecker.frames.Frame;
import tablechecker.frames.ISlot;

public class Pair implements Serializable
{

  private Frame frame;
  private ISlot slot;
  private Value value;

  public Pair(Frame frame, ISlot slot, Value value)
  {
    this.frame = frame;
    this.slot = slot;
    this.value = value;
  }

  public Frame getFrame()
  {
    return frame;
  }

  public ISlot getSlot()
  {
    return slot;
  }

  public Value getValue()
  {
    return value;
  }

  public void setValue(Value value)
  {
    this.value = value;
  }

  public void setSlot(ISlot slot)
  {
    this.slot = slot;
  }

  public void setFrame(Frame frame)
  {
    this.frame = frame;
  }
}
