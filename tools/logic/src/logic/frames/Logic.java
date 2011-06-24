/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package logic.frames;

//import Core.Logic.Product.InfMech;
import logic.product.Rule;
import logic.product.Value;
import java.util.HashMap;
import java.util.ArrayList;
import javax.swing.tree.DefaultMutableTreeNode;

/**
 *
 * @author nazgul
 */
public class Logic
{
  protected HashMap<String, String> duplicate;
  protected int agent;
 
  /**
   * Construct new logic
   */
  public Logic()
  {
  }

  private String getPath(ArrayList<String> path)
  {
    String result = "";

    for (String x : path)
    {
      if (!result.equals(""))
        result += ".";
      result += x;
    }

    return result;
  }

  private boolean bindEnumSlot(ISlot slot, HashMap<String, String> data, DefaultMutableTreeNode root, ArrayList<String> path)
  {
    String spath = getPath(path);

    String val = data.get(spath);
    String slotVal = slot.getValue().getValue();

    root.add(new DefaultMutableTreeNode("Путь: " + spath));

    if (val == null)
      return slotVal == null || slotVal.equals("Неважно") || slotVal.equals("Неизвестно");

    if (slotVal == null)
      return val.equals("Неважно") || val.equals("Неизвестно");

    root.add(new DefaultMutableTreeNode("Экземпляр: " + val + ", прототип: " + slotVal));

    if (val.equals("Неважно") || slotVal.equals("Неважно"))
      return true;

    return slotVal.equals(val);
  }

  private boolean bindSubframeSlot(ISlot slot, HashMap<String, String> data, DefaultMutableTreeNode root, ArrayList<String> path)
  {
    Link link = slot.getInLink();

    if (link == null || link.getSource() == null) /* Assume void-linked subframe was binded */
      return true;

    Frame frame = link.getSource();

    DefaultMutableTreeNode node = new DefaultMutableTreeNode("Слот " + slot.getName() + " - субфрейм(" + frame.getName() + "), переход к привязке субфрейма");
    root.add(node);

    return bindFrame(frame, data, node, path);
  }

  protected void setSlotValue(ISlot slot, String value)
  {
    Value val = slot.getValue().getDomen().getValueByName(value);
    slot.setValue(val);
  }

  private void setWholeHier(ISlot slot, String value)
  {
    ArrayList<Frame> allFrames = Frameset.getInstance().getAllFrames();
    Frame pFrame = slot.getParent();

    setSlotValue(slot, value);

    for (Frame frame : allFrames)
    {
      if (frame.isInstanceOf(pFrame) || pFrame.isInstanceOf(frame)) {
        ISlot sl = frame.getSlotByName(slot.getName());

        if(sl != null)
          setSlotValue(sl, value);
      }
    }
  }

  private boolean bindProductionalSlot(ISlot slot, HashMap<String, String> data, DefaultMutableTreeNode root, ArrayList<String> path)
  {
    ArrayList<Rule> rules = slot.getRules();
//    InfMech infMech = new InfMech();
    ISlot goalSlot = slot.getGoalSlot();

    data.remove(goalSlot.getName());

    if(goalSlot.getName().equals("Расположение реципиента")) {
      Frame frame = Frameset.getInstance().getFrameByName("ТС Агента");
      if (frame != null) {
        setWholeHier(frame.getSlotByName("X"), data.get("Агент.X"));
        setWholeHier(frame.getSlotByName("Y"), data.get("Агент.Y"));
      }

      frame = Frameset.getInstance().getFrameByName("ТС Реципиента");
      if (frame != null) {
        setWholeHier(frame.getSlotByName("X"), data.get("Реципиент.X"));
        setWholeHier(frame.getSlotByName("Y"), data.get("Реципиент.Y"));
      }
    }

    ArrayList<String> nPath = new ArrayList<String>();
    nPath.addAll(path);
    nPath.set(nPath.size() - 1, goalSlot.getName());
    String sPath = getPath(nPath);
    data.remove(sPath);


//    if (infMech.start(goalSlot, rules, data, sPath, root)) {
//      return true;
//    }

    return false;
  }

  private boolean bindSlot(ISlot slot, HashMap<String, String> data, DefaultMutableTreeNode root, ArrayList<String> path)
  {
    boolean result = true;

    if(slot.getType() == Slot.IMAGE)
      return true;

    path.add(slot.getName());

    DefaultMutableTreeNode node = new DefaultMutableTreeNode("Попытка привязать слот " + slot.getName());
    root.add(node);

    switch (slot.getType())
    {
      case Slot.ENUM:
        result = bindEnumSlot(slot, data, node, path);
        break;
      case Slot.SUBFRAME:
        result = bindSubframeSlot(slot ,data, node, path);
        break;
      case Slot.PRODUCTIONAL:
        result = bindProductionalSlot(slot ,data, node, path);
        break;
    }

    if (result)
      node.add(new DefaultMutableTreeNode("Слот " + slot.getName() + " был успешно привязан"));
    else
      node.add(new DefaultMutableTreeNode("Слот " + slot.getName() + " не был привязан"));

    path.remove(path.size() - 1);

    return result;
  }

  private void bindSubSituations(HashMap<String, String> data, DefaultMutableTreeNode root)
  {
    ArrayList<Frame> rootFrames = getSubRoots();
    Frame rootFrame = getRootFrame();
    ArrayList<String> path = new ArrayList<String> ();

    int recipient = 0;

    for (;;) {
      if (recipient == agent) {
        recipient++;
        continue;
      }

      HashMap<String, String> currentData = getCurrentData(data, agent, recipient);

      if(currentData == null) /* no more agents to review */
        break;

      DefaultMutableTreeNode node = new DefaultMutableTreeNode("Реципиент: ТС " + Integer.toString(recipient + 1));
      root.add(node);

      for (Frame f : rootFrames) {
        if (bindFrame(f, currentData, node, path)) {
          ISlot actSlot = f.getSlotByName("Привязался");

          if(actSlot != null)
            setSlotValue(actSlot, "Да");
        }
      }

      recipient++;
    }
  }

  private boolean bindFrame(Frame frame, HashMap<String, String> data, DefaultMutableTreeNode root, ArrayList<String> path)
  {
    ArrayList<ISlot> slots = frame.getSlots();
    String spath = getPath(path);
    boolean ok = false;

    DefaultMutableTreeNode node = new DefaultMutableTreeNode("Попытка привязать фрейм " + frame.getName());
    root.add(node);

    if (spath.length() > 0)
    {
      for (String key : data.keySet())
      {
        if (key.startsWith(spath + ".") || key.equals(spath)) {
          ok = true;
          break;
        }
      }
    } else ok = true;

    if (!ok)
    {
      ISlot slot = frame.getSlotByName("Присутствие");

      if (slot != null && (slot.getValue().getValue().equals("Нет") || slot.getValue().getValue().equals("Неважно")))
      {
        node.add(new DefaultMutableTreeNode("Фрейм " + frame.getName() + " был успешно привязан"));
        return true;
      }

      /* try to bind each slot */
      boolean couldIgnore = true;
      for (ISlot tslot : slots) {
        switch (tslot.getType()) {
          case Slot.ENUM:
          case Slot.PRODUCTIONAL:
            couldIgnore = false;
            break;
          case Slot.SUBFRAME:
            Link link = tslot.getInLink();

            if (link == null || link.getSource() == null) /* Assume void-linked subframe was binded */
              return true;

            Frame dframe = link.getSource();
            if (dframe != null) {
              ISlot pSlot = dframe.getSlotByName("Присутствие");
              if (pSlot == null || pSlot.getValue() == null ||
                      (!pSlot.getValue().getValue().equals("Нет") && !pSlot.getValue().getValue().equals("Неважно")))
                couldIgnore = false;
            }

            break;
        }
      }

      if(couldIgnore) {
        node.add(new DefaultMutableTreeNode("Фрейм " + frame.getName() + " был успешно привязан"));
        return true;
      }

      node.add(new DefaultMutableTreeNode("Фрейм " + frame.getName() + " не был привязан (отсутствует в экземпляре)"));

      return false;
    } else {
      ISlot slot = frame.getSlotByName("Присутствие");

      if (slot != null && slot.getValue().getValue().equals("Нет"))
      {
        node.add(new DefaultMutableTreeNode("Фрейм " + frame.getName() + " не был привязан"));
        return false;
      }
    }

    /* try to bind each slot */
    for (ISlot slot : slots)
    {
      // XXX: hack for action slot in situation frame
      if (slot.getName().equals("Действие") || slot.getName().equals("X") || slot.getName().equals("Y") ||
              slot.getName().equals("Привязался"))
        continue;

      if(slot.getName().equals("Присутствие")) /* was handled upper */
        continue;

      if(slot.getName().equals("Подситуации")) {
        DefaultMutableTreeNode node1 = new DefaultMutableTreeNode("Подситуации");
        bindSubSituations(data, node1);
        node.add(node1);
        continue;
      }

      if (!bindSlot(slot, data, node, path)) {
        node.add(new DefaultMutableTreeNode("Фрейм " + frame.getName() + " не был привязан"));
        return false;
      }
    }

    node.add(new DefaultMutableTreeNode("Фрейм " + frame.getName() + " был успешно привязан"));

    return true;
  }

  private Frame getSubRootFrame()
  {
    Frameset frameset = Frameset.getInstance();
    /* XXX: replace with something smarter */
    return frameset.getFrameByName("Подситуация");
  }

  private Frame getRootFrame()
  {
    Frameset frameset = Frameset.getInstance();
    return frameset.getFrameByName("Ситуация");
  }

  private HashMap<String, String> getCurrentData(HashMap<String, String> data, int agentNumber, int recipientNumber)
  {
    HashMap<String, String> result = new HashMap<String, String>();
    int a = 0, rnum = 0;
    boolean agentFound = false;
    boolean recipientFound = false;
    HashMap<Integer, int[]> coords = new HashMap<Integer, int[]> ();

    for (String key : data.keySet())
    {
      String val = data.get(key);

      if(key.startsWith("Транспортное средство")) {
        String prefix = "";
        a = new Integer(key.substring(22, key.indexOf('.')));

        if (a == agentNumber) {
          prefix = "Агент";
          agentFound = true;
        } else {

          if (a != recipientNumber)
            continue;

          prefix = "Реципиент";
          recipientFound = true;
        }

        String suffix = key.substring(key.indexOf('.'));
        result.put(prefix + suffix, val);
      } else {
        result.put(key, val);
      }
    }

    if(!agentFound || !recipientFound)
      return null;

    return result;
  }

  private ArrayList<Frame> getSubRoots()
  {
    Frame root = getSubRootFrame();

    if(root == null)
      return null;

    ArrayList<Frame> frames = new ArrayList<Frame> ();

    ArrayList<Frame> allFrames = Frameset.getInstance().getAllFrames();
    for (Frame f : allFrames) {
      if (f.isInstanceOf(root) && f != root)
        frames.add(f);
    }

    return frames;
  }

  private int[] buildPoint(HashMap<String, String> data, int agent)
  {
    String i = Integer.toString(agent);
    String a = "Транспортное средство " + i;

    if (data.get("Агент.X") != null && agent == -1)
      return new int[] {new Integer(data.get("Агент.X")), new Integer(data.get("Агент.Y"))};
    else if (data.get(a + ".X") != null)
      return new int[] {new Integer(data.get(a + ".X")), new Integer(data.get(a + ".Y"))};

    return null;
  }

  /**
   * Run PDD frame resolving 
   */
  public int[] run(HashMap<String, String> data, DefaultMutableTreeNode root)
  {
    ArrayList<Frame> rootFrames = getSubRoots();
    Frame rootFrame = getRootFrame();
    ArrayList<String> path = new ArrayList<String> ();

    if (rootFrames == null || rootFrame == null) /* notfing to do */
      return null;

    int curAgent = 0;
    int[] pos = null;

    for (;;) {
      boolean agentHandled = false;

      boolean ok = false;
      for (String k : data.keySet()) {
        if (k.startsWith("Транспортное средство " + Integer.toString(curAgent))) {
          ok = true;
          break;
        }
      }

      if(!ok)
        break;

      for (Frame f : rootFrames) {
        ISlot actSlot = f.getSlotByName("Привязался");

        if(actSlot != null)
          setSlotValue(actSlot, "Нет");
      }

      agent = curAgent;
      DefaultMutableTreeNode node = new DefaultMutableTreeNode("Вывод действия для ТС " + Integer.toString(curAgent + 1));
      root.add(node);

      if (bindFrame(rootFrame, data, node, path)) {
        if (data.get("Действие") != null && data.get("Действие").equals("Продолжать движение"))
          return buildPoint(data, agent);
      } else {
        pos = buildPoint(data, agent);
      }

      curAgent++;
    }

    if(pos == null)
      pos = buildPoint(data, 0);

    return pos;
  }
}
