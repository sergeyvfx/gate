package logic.product;

import logic.frames.Frame;
import logic.frames.ISlot;
import java.io.Serializable;

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
