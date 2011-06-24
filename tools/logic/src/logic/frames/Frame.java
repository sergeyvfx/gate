package logic.frames;

import logic.product.Pair;
import logic.product.Rule;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.HashMap;

public class Frame implements Serializable
{

  protected ArrayList<Slot> slots; /* Frame's slots */

  protected String name; /* Frame's name */
  protected String comment; /* Comment for frame */

  protected ArrayList<Link> inLinks;
  protected ArrayList<Link> outLinks;

  protected HashMap<String, Object> slotCustoms;

  /**
   * Constructor
   */
  public Frame()
  {
    slots = new ArrayList<Slot>();
    inLinks = new ArrayList<Link>();
    outLinks = new ArrayList<Link>();
    slotCustoms = new HashMap<String, Object>();
  }

  /**
   * Constructor
   *
   * @param name - name for frame
   */
  public Frame(String name)
  {
    this();

    this.name = name;
  }

  /**
   * Get frame's name
   *
   * @return frame's name
   */
  public String getName()
  {
    return name;
  }

  /**
   * Set frame's name
   *
   * @param name - new frame's name
   */
  public void setName(String name)
  {
    Frame otherFrame = Frameset.getInstance().getFrameByName(name);
    if (otherFrame == null || otherFrame == this)
    {
      this.name = name;
    } else {
//      _System.getInstance().showErrorMsg("Frame with this name is already exists");
    }
  }

  /**
   * Get all slots
   *
   * @return all slots
   */
  public ArrayList<Slot> getOwnSlots()
  {
    return slots;
  }

  /**
   * Get number of slots
   *
   * @return total number of slots in the frame
   */
  public int getOwnSlotCount()
  {
    return slots.size();
  }

  /**
   * Get slot by index
   *
   * @param index - index of slot to get
   * @return return slot with specified index
   */
  public Slot getOwnSlotByIndex(int index)
  {
    return slots.get(index);
  }

  /**
   * Get own slot by name
   *
   * @param name - name of slot to get
   * @return return slot with specified name
   */
  public Slot getOwnSlotByName(String name)
  {
    name = name.trim().toLowerCase();

    for (int i = 0, n = slots.size(); i < n; ++i)
    {
      Slot slot = slots.get(i);
      if (slot.getName().trim().toLowerCase().equals(name))
      {
        return slot;
      }
    }

    return null;
  }

  /**
   * Get slot by name
   *
   * @param name - name of slot to get
   * @return return slot with specified name
   */
  public ISlot getSlotByName(String name)
  {
    name = name.trim().toLowerCase();
    ArrayList<ISlot> slots = getSlots();

    for (int i = 0, n = slots.size(); i < n; ++i)
    {
      ISlot slot = slots.get(i);
      if (slot.getName().trim().toLowerCase().equals(name))
      {
        return slot;
      }
    }

    return null;
  }

  /**
   * Create new slot
   *
   * @param name - name for new slot
   * @return new slot object
   */
  public Slot createSlot(String name)
  {
    if (getOwnSlotByName(name) != null)
    {
      /* Slot with such name already exists */
      return null;
    }

    Slot slot = new Slot(this, name);
    slots.add(slot);
//    frameEditor.Utils.updateDepgraph();

    return slot;
  }

  private boolean canRemoveSlot(Slot slot)
  {
    ArrayList<Frame> frames = Frameset.getInstance().getAllFrames();
    for (Frame f : frames)
    {
      ArrayList<Slot> slots = f.getOwnSlots();
      for (Slot s : slots)
      {
        if (s.getType() == Slot.PRODUCTIONAL)
        {
          ArrayList<Rule> rules = s.getRules();
          for (Rule r : rules)
          {
            ArrayList<Pair> ifPart = r.getIfPart();
            for (Pair p : ifPart)
            {
              if (p.getSlot().equals(slot))
              {
                return false;
              }
            }
            ArrayList<Pair> thenPart = r.getThenPart();
            for (Pair p : thenPart)
            {
              if (p.getSlot().equals(slot))
              {
                return false;
              }
            }
          }
        }
      }
    }
    return true;
  }
  /**
   * Remove slot
   *
   * @param slot - slot which would be deleted
   */
  public boolean removeSlot(Slot slot)
  {
    if (canRemoveSlot(slot))
    {
      return slots.remove(slot);
    }
    return false;
  }

  /**
   * Remove slot
   *
   * @param name - name of slot which would be deleted
   */
  public void removeSlot(String name)
  {
    removeSlot(getOwnSlotByName(name));
  }

  @Override
  public String toString()
  {
    //return "<Frame instance name=" + name + " slots=" + slots + ">";
    return name;
  }

  public boolean hasIncommingLink(Object from)
  {
    for (Link l : inLinks)
    {
      if (l.getSource() == from)
      {
        return true;
      }
    }
    return false;
  }

  public boolean hasOutgoingLink(Object to)
  {
    for (Link l : outLinks)
    {
      if (l.getTarget() == to)
      {
        return true;
      }
    }
    return false;
  }

  public boolean hasIncommingLink(int type)
  {
    for (Link l : inLinks)
    {
      if (l.getType() == type)
      {
        return true;
      }
    }
    return false;
  }

  public boolean hasOutgoingLink(int type)
  {
    for (Link l : outLinks)
    {
      if (l.getType() == type)
      {
        return true;
      }
    }
    return false;
  }

  public void addInLink(Link l)
  {
    inLinks.add(l);
  }

  public void removeInLink(Frame from)
  {
    for (Link l : inLinks)
    {
      if (l.getSource() == from)
      {
        inLinks.remove(l);
        return;
      }
    }
  }

  public void addOutLink(Link l)
  {
    outLinks.add(l);
  }

  public void removeOutLinkTo(Object to)
  {
    for (Link l : outLinks)
    {
      if (l.getTarget() == to)
      {
        outLinks.remove(l);
        return;
      }
    }
  }

  public ArrayList<Link> getOutLinks()
  {
    return outLinks;
  }

  public ArrayList<Link> getInLinks()
  {
    return inLinks;
  }

  public Frame getPrototype()
  {
    for (Link l : outLinks)
    {
      if (l.getType() == Link.IS_A)
      {
        return (Frame)l.getTarget();
      }
    }

    return null;
  }

  public ArrayList<ISlot> getSlots()
  {
    Frame prototype = getPrototype();
    ArrayList<ISlot> result = new ArrayList<ISlot>();

    for (Slot slot : slots)
    {
      result.add(slot);
    }

    if (prototype != null)
    {
      ArrayList<ISlot> protoSlots = prototype.getSlots();
      for (ISlot slot : protoSlots)
      {
        if (!result.contains(slot))
        {
          VirtualSlot vSlot = new VirtualSlot(this, slot);
          result.add(vSlot);
        }
      }
    }

    return result;
  }

  public Object getSlotCusotm(String name)
  {
    if (slotCustoms.containsKey(name)) {
      return slotCustoms.get(name);
    }

    return null;
  }

  public void createSlotCusotm(String name, Object obj)
  {
    if (slotCustoms.containsKey(name)) {
      return;
    }

    slotCustoms.put(name, obj);
  }

  public String getComment()
  {
    return comment;
  }

  public void setComment(String comment)
  {
    this.comment = comment;
  }

  public boolean isInstanceOf(Frame f)
  {
    if (f == this)
      return true;

    ArrayList<Link> links = getOutLinks();

    for (Link l : links)
    {
      if (l.getType() != Link.IS_A)
        continue;

      Frame parent = (Frame)l.getTarget();

      if (f == parent)
        return true;

      if (parent.isInstanceOf(f))
        return true;
    }

    return false;
  }
}
