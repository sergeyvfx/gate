package tablechecker.frames;

import java.util.ArrayList;
import tablechecker.frames.logic.Rule;
import tablechecker.frames.logic.Value;

public interface ISlot {

  public String getName();

  public void setName(String name);

  public Frame getParent();

  public void setDefaultValue(Object value);

  public Object getDefaultValue();

  public boolean hasIncommingLink(int type);

  public void addInLink(Link l);

  public void removeInLink(Link l);

  public void removeInLink(Frame from);

  public void setType(int type);

  public int getType();

  public Link getInLink();

  public Link getOwnInLink();

  public Value getValue();

  public void setValue(Value value);

  public ArrayList<Rule> getRules();

  public void setRules(ArrayList<Rule> rules);

  public void setGoalSlot(ISlot slot);

  public ISlot getGoalSlot();

  public String getPathToImage();

  public void setPathToImage(String pathToImage);
}
