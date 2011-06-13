package tablechecker.frames;

import java.io.Serializable;
import java.util.ArrayList;
import tablechecker.frames.logic.Rule;
import tablechecker.frames.logic.Value;

public class VirtualSlot
        implements ISlot, Serializable {

  protected ISlot origSlot = null;
  protected Frame parent = null;

  protected class VirtualBlock
          implements Serializable {

    public Value value = null;
    public Link link = null;
    public String pathToImage = null;
    public ArrayList<Rule> rules = null;
    public ISlot goalSlot = null;
  }

  public VirtualSlot(Frame parent, ISlot origSlot) {
    this.parent = parent;
    this.origSlot = origSlot;
  }

  /******
   * 
   */
  protected Object getCustomBlock() {
    Object block = parent.getSlotCusotm(getName());

    if (block == null) {
      block = new VirtualBlock();
      parent.createSlotCusotm(getName(), block);
    }

    return block;
  }

  @Override
  public String getPathToImage() {
    String value = origSlot.getPathToImage();
    VirtualBlock block = (VirtualBlock) getCustomBlock();

    if (block.pathToImage != null) {
      value = block.pathToImage;
    }

    return value;
  }

  @Override
  public void setPathToImage(String pathToImage) {
    VirtualBlock block = (VirtualBlock) getCustomBlock();
    block.pathToImage = pathToImage;
  }

  @Override
  public Value getValue() {
    Value value = origSlot.getValue();
    VirtualBlock block = (VirtualBlock) getCustomBlock();

    if (block.value != null) {
      value = block.value;
    }

    return value;
  }

  @Override
  public void setValue(Value value) {
    VirtualBlock block = (VirtualBlock) getCustomBlock();
    block.value = value;
  }

  @Override
  public ArrayList<Rule> getRules() {
    ArrayList<Rule> rules = origSlot.getRules();
    VirtualBlock block = (VirtualBlock) getCustomBlock();

    if (block.rules != null) {
      rules = block.rules;
    }

    return rules;
  }

  @Override
  public void setRules(ArrayList<Rule> rules) {
    VirtualBlock block = (VirtualBlock) getCustomBlock();
    block.rules = rules;
  }

  @Override
  public void setGoalSlot(ISlot slot) {
    VirtualBlock block = (VirtualBlock) getCustomBlock();
    block.goalSlot = slot;
  }

  @Override
  public ISlot getGoalSlot() {
    ISlot slot = origSlot.getGoalSlot();
    VirtualBlock block = (VirtualBlock) getCustomBlock();

    if (block.goalSlot != null) {
      slot = block.goalSlot;
    }

    return slot;
  }

  @Override
  public Link getOwnInLink() {
    VirtualBlock block = (VirtualBlock) getCustomBlock();
    return block.link;
  }

  @Override
  public Link getInLink() {
    VirtualBlock block = (VirtualBlock) getCustomBlock();

    if (block.link != null) {
      return block.link;
    }

    return origSlot.getInLink();
  }

  @Override
  public boolean hasIncommingLink(int type) {
    VirtualBlock block = (VirtualBlock) getCustomBlock();

    if (block.link != null) {
      return true;
    }

    return false;
  }

  @Override
  public void addInLink(Link l) {
    Link orig = origSlot.getInLink();
    VirtualBlock block = (VirtualBlock) getCustomBlock();

    block.link = l;
  }

  @Override
  public void removeInLink(Link l) {
    VirtualBlock block = (VirtualBlock) getCustomBlock();
    block.link = null;
  }

  @Override
  public void removeInLink(Frame from) {
    VirtualBlock block = (VirtualBlock) getCustomBlock();

    if (block.link != null && block.link.getSource() == from) {
      block.link = null;
    } else {
      origSlot.removeInLink(from);
    }
  }

  @Override
  public Frame getParent() {
    return parent;
  }

  /*****
   * Redirect to orig slot
   */
  @Override
  public String getName() {
    return origSlot.getName();
  }

  @Override
  public void setName(String name) {
    origSlot.setName(name);
  }

  @Override
  public void setDefaultValue(Object value) {
    origSlot.setDefaultValue(value);
  }

  @Override
  public Object getDefaultValue() {
    return origSlot.getDefaultValue();
  }

  @Override
  public void setType(int type) {
    origSlot.setType(type);
  }

  @Override
  public int getType() {
    return origSlot.getType();
  }

  @Override
  public String toString() {
    return origSlot.toString();
  }
}
