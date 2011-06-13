package tablechecker.frames;

import java.io.Serializable;
import java.util.ArrayList;
import tablechecker.frames.logic.Rule;
import tablechecker.frames.logic.Value;

public class Slot
        implements Serializable, ISlot {

  /* For all slot's type */
  protected String name; /* Slot's name */

  protected Frame parent; /* Slot's parent */

  /* Slot's types */
  protected int type;
  public static final int ENUM = 0, SUBFRAME = 1, PRODUCTIONAL = 2, IMAGE = 3;

  /* Only for ENUM type */
  protected Value value;

  /* Only for SUBFRAME */
  protected ArrayList<Link> inLinks;

  /* Only for PRODUCTIONAL */
  protected ArrayList<Rule> rules;
  protected ISlot goalSlot;

  /* Onlt for IMAGE */
  protected String pathToImage;
  //TODO а это зачем?
  protected Object defaultValue;

  /**
   * Constructor
   */
  public Slot(Frame parent) {
    this.parent = parent;
    defaultValue = null;
    inLinks = new ArrayList<Link>();
    type = ENUM;
  }

  /**
   * Constructor
   *
   * @param name - name for slot
   */
  public Slot(Frame parent, String name) {
    this(parent);
    this.name = name;
  }

  /**
   * Get slots's name
   *
   * @return slots's name
   */
  @Override
  public String getName() {
    return name;
  }

  /**
   * Set slots's name
   *
   * @param name - new slot's name
   */
  @Override
  public void setName(String name) {
    Slot otherSlot = this.parent.getOwnSlotByName(name);

    if (otherSlot == this || otherSlot == null) {
      this.name = name;
    }
  }

  /**
   * Get slot's parent
   *
   * @return frame -- slot's parent
   */
  @Override
  public Frame getParent() {
    return parent;
  }

  /**
   * Set slot's default value
   *
   * @param value - new default value
   */
  @Override
  public void setDefaultValue(Object value) {
    defaultValue = value;
  }

  /**
   * Get slot's default value
   *
   * @return slot's default value
   */
  @Override
  public Object getDefaultValue() {
    return defaultValue;
  }

  @Override
  public String toString() {
//    return "<Slot instance name=" + name + ">";
    return name;
  }

  @Override
  public boolean hasIncommingLink(int type) {
    for (Link l : inLinks) {
      if (l.getType() == type) {
        return true;
      }
    }
    return false;
  }

  @Override
  public void addInLink(Link l) {
    inLinks.add(l);
  }

  @Override
  public void removeInLink(Link l) {
    inLinks.remove(l);
  }

  @Override
  public void removeInLink(Frame from) {
    for (Link l : inLinks) {
      if (l.getSource() == from) {
        inLinks.remove(l);
        return;
      }
    }
  }

  /**
   * Set slot's type
   *
   * @param type - new slot type
   */
  @Override
  public void setType(int type) {
    this.type = type;
  }

  /**
   * Get slot type
   *
   * @return slot's type
   */
  @Override
  public int getType() {
    return this.type;
  }

  @Override
  public Link getInLink() {
    if (inLinks.isEmpty()) {
      return null;
    } else {
      return inLinks.get(0);
    }
  }

  @Override
  public Link getOwnInLink() {
    return getInLink();
  }

  @Override
  public Value getValue() {
    return value;
  }

  @Override
  public void setValue(Value value) {
    this.value = value;
  }

  @Override
  public ArrayList<Rule> getRules() {
    return rules;
  }

  @Override
  public void setRules(ArrayList<Rule> rules) {
    this.rules = rules;
  }

  @Override
  public void setGoalSlot(ISlot slot) {
    this.goalSlot = slot;
  }

  @Override
  public ISlot getGoalSlot() {
    return goalSlot;
  }

  @Override
  public String getPathToImage() {
    return pathToImage;
  }

  @Override
  public void setPathToImage(String pathToImage) {
    this.pathToImage = pathToImage;
  }
}
