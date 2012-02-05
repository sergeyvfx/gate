package logic.frames;

import com.thoughtworks.xstream.XStream;
import com.thoughtworks.xstream.io.xml.DomDriver;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import logic.product.Domen;
import java.io.Serializable;
import java.io.Writer;
import java.util.ArrayList;

public class Frameset implements Serializable {

  protected ArrayList<Frame> frames; /* all frames */

  protected ArrayList<Domen> domens; /* all domens */

  protected Frame activeFrame;
  static protected Frameset instance = null;

  public static Frameset getInstance() {
    if (instance == null) {
      instance = new Frameset();
    }

    return instance;
  }

  public static void setInstance(Frameset inst) {
    instance = inst;
  }

  public Frameset() {
    frames = new ArrayList<Frame>();
    domens = new ArrayList<Domen>();
    activeFrame = null;
  }

  /**
   * Get all frames
   *
   * @return all frames
   */
  public ArrayList<Frame> getAllFrames() {
    return frames;
  }

  /**
   * Get number of frames
   *
   * @return total number of frames in the set
   */
  public int getFrameCount() {
    return frames.size();
  }

  /**
   * Get frame by index
   *
   * @param index - index of frame to get
   * @return return frame with specified index
   */
  public Frame getFrameByIndex(int index) {
    return frames.get(index);
  }

  /**
   * Get frame by name
   *
   * @param name - name of frame to get
   * @return return frame with specified name
   */
  public Frame getFrameByName(String name) {
    name = name.trim().toLowerCase();

    for (int i = 0, n = frames.size(); i < n; ++i) {
      Frame frame = frames.get(i);
      if (frame.getName().trim().toLowerCase().equals(name)) {
        return frame;
      }
    }

    return null;
  }

  /**
   * Create new frame
   *
   * @param name - name for new frame
   * @return new frame object
   */
  public Frame createFrame(String name) {
    if (getFrameByName(name) != null) {
      /* Frame with such name already exists */
      return null;
    }

    Frame frame = new Frame(name);
    frames.add(frame);

    return frame;
  }

  public boolean isLink(Frame source, Object target) {
    return source.hasOutgoingLink(target);
  }

  public boolean isPath(Frame source, Object target) {
    if (source == target) {
      return true;
    } else if (target instanceof ISlot) {
      return false;
    } else {
      ArrayList<Link> links = ((Frame) target).getOutLinks();
      for (Link l : links) {
        Object o = l.getTarget();
        if (isPath(source, o)) {
          return true;
        }
      }
      return false;
    }
  }

  public boolean isMultipleInheritance(Frame source) {
    return source.hasIncommingLink(Link.IS_A);
  }

  public Link createLink(Frame source, Object target, int type) throws Exception {
    Link l = new Link(source, target, type);
    /**
     * Между двумя сущностями связь может быть только одна
     */
    if (isPath(source, target)) {
      throw new Exception("Link between this vertex is already exists");
    }
    if (target instanceof Frame) {
      switch (type) {
        case Link.IS_A:
          if (source.hasOutgoingLink(Link.IS_A)) {
            throw new Exception("Only one link with type IS A allowed");
          }
          break;
        case Link.A_PART_OF:
          throw new Exception("This type of link is not allowed");
        case Link.SUB_FRAME:
          throw new Exception("This type of link is not allowed");
      }
      source.addOutLink(l);
      ((Frame) target).addInLink(l);
    } else if (target instanceof ISlot) {
      ISlot s = (ISlot) target;
      switch (type) {
        case Link.IS_A:
          throw new Exception("This type of link is not allowed");
        case Link.A_PART_OF:
          if (s.hasIncommingLink(Link.A_PART_OF)) {
            throw new Exception("Only one link with type A PART OF is allowed");
          }
          break;
        case Link.SUB_FRAME:
          if (s.hasIncommingLink(Link.SUB_FRAME)) {
            throw new Exception("Only one link with type SUB FRAME is allowed");
          }
      }
      source.addOutLink(l);
      s.addInLink(l);
    }

    return l;
  }

  public void removeLink(Frame source, Object target) {
    if (isLink(source, target)) {
      source.removeOutLinkTo(target);
      if (target instanceof Frame) {
        ((Frame) target).removeInLink(source);
      } else if (target instanceof ISlot) {
        ((ISlot) target).removeInLink(source);
      }
    }
  }

  public void removeLink(Link l) {
    removeLink(l.getSource(), l.getTarget());
  }

  /**
   * Delete frame
   *
   * @param frame - frame which would be deleted
   */
  public void deleteFrame(Frame frame) {
    if (frame == activeFrame) {
      setActiveFrame(null);
    }

    frames.remove(frame);
  }

  /**
   * Delete frame
   *
   * @param name - name of frame which would be deleted
   */
  public void deleteFrame(String name) {
    deleteFrame(getFrameByName(name));
  }

  @Override
  public String toString() {
    return "<Frameset instance frames=" + frames + ">";
  }

  /**
   * Set active frame
   *
   * @param frame - new active frame
   */
  public void setActiveFrame(Frame frame) {
    activeFrame = frame;
  }

  /**
   * Get active frame
   *
   * @return active frame
   */
  public Frame getActiveFrame() {
    return activeFrame;
  }

  /**
   * Clear frame set
   */
  public void clear() {
    frames.clear();
    domens.clear();
    activeFrame = null;
  }

  public ArrayList<Domen> getDomens() {
    return domens;
  }

  public String[] getDomainNames() {
    String[] names = new String[domens.size()];
    int i = 0;

    for (Object d : domens) {
      names[i++] = d.toString();
    }

    return names;
  }

  public void setDomens(ArrayList<Domen> domens) {
    this.domens = domens;
  }

  public void addDomens(Domen domen) {
    domens.add(domen);
  }

  public void addDomen(Domen domen) {
    domens.add(domen);
  }

  public void insertDomen(int index, Domen domen) {
    domens.add(index, domen);
  }

  public Domen getDomenByName(String name) {
    for (Domen d : domens) {
      if (d.getName().equals(name)) {
        return d;
      }
    }
    return null;
  }

  public boolean isDomen(Domen domen, String name) {
    boolean res = true;
    for (Domen d : domens) {
      res = res && (!d.getName().equalsIgnoreCase(name) || d == domen);
    }
    return !res;
  }

  private boolean canRemoveDomen(Domen domen) {
    for (Frame f : frames) {
      ArrayList<Slot> slots = f.getOwnSlots();
      for (Slot s : slots) {
        if (s.getValue().getDomen().equals(domen)) {
          return false;
        }
      }
    }
    return true;
  }

  /**
   * Удаляет домен из списка доменов
   * Внимание: не производится проверка на корректность удаления
   * @param index индекс домена для удаления
   */
  public void removeDomen(int index) {
    domens.remove(index);
  }

  public boolean removeDomen(Domen domen) {
    if (canRemoveDomen(domen)) {
      return domens.remove(domen);
    }
    return false;
  }

  public int indexOfDomen(Domen domen) {
    return domens.indexOf(domen);
  }

  public void save(File f) throws FileNotFoundException, IOException {
    if (!f.getCanonicalPath().endsWith(".frs")) {
      f = new File(f.getCanonicalPath() + ".frs");
    }
    Writer out = new OutputStreamWriter(new FileOutputStream(f));
    XStream xstream = new XStream(new DomDriver());
    String xml = xstream.toXML(getInstance());
    out.write(xml);
    out.flush();
    out.close();
  }

  public void load(File f) throws IOException {
    FileInputStream in = new FileInputStream(f);
    byte[] fileData = new byte[in.available()];
    in.read(fileData);
    in.close();
    String xml = new String(fileData);
    XStream xstream = new XStream(new DomDriver());
    Frameset.setInstance((Frameset) xstream.fromXML(xml));
  }
}
