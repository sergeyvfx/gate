package Core;

import logic.product.Domen;
import logic.frames.Frameset;
import logic.frames.ISlot;
import logic.frames.Link;
import logic.frames.Slot;
import logic.frames.Frame;
import UI.MainForm.OptionsUpdater;
import UI.jTreeTable.RowEditorModel;
import java.util.ArrayList;
import java.util.List;
import javax.swing.tree.DefaultMutableTreeNode;
import frameEditor.Utils;
import frameEditor._System;

public class Options
{
  private OptionEntry root;
  private Frame frame;
  private GroupEntry slotsParent = null;
  private OptionsUpdater updater;

  protected String[] slot_types = {"Перечисление", "Субфрейм", "Продукция", "Изображение", "Текст"};

  public Object getSlotsParent()
  {
    return slotsParent;
  }

  private int fillSlot(ISlot slot, DefaultMutableTreeNode parent, RowEditorModel rm, int index)
  {
    boolean origSlot = slot instanceof Slot;

    List<Object> userData = new ArrayList<Object> ();
    String name = slot.getName();
    GroupEntry grpSlot = new GroupEntry(name);
    parent.add(grpSlot);

    userData.add(slot);
    userData.add(grpSlot);
    ++index;

    grpSlot.setTag(slot.getName());

    if (origSlot) {
      /* Slot's name */
      OptionEntry optName = new OptionEntry(new ValueSetter (userData) {
        @Override
        public Object setValue(Object value)
        {
          ISlot slot = (ISlot)((ArrayList<Object>)this.userData).get(0);

          GroupEntry grpSlot = (GroupEntry)((ArrayList<Object>)this.userData).get(1);
          slot.setName((String)value);

          String name =  slot.getName();
          grpSlot.SetName(name);
          grpSlot.setTag(name);

          return name;
        }
      });

      optName.setOption("Имя");
      optName.setValue(name);
      grpSlot.add(optName);
      ++index;

      userData = new ArrayList<Object> ();
      userData.add(slot);
      /* Slot type */
      OptionEntry optType = new OptionEntry(new ValueSetter (slot) {
        @Override
        public Object setValue(Object value)
        {
          ISlot slot = (ISlot)userData;

          int type = 0;
          for (; type < slot_types.length; ++type)
          {
            if (slot_types[type].equals(value))
            {
              if (type != Slot.SUBFRAME) {
                Link l = slot.getInLink();
                if (l != null ) {
                  Frameset.getInstance().removeLink(l);
                  _System.getInstance().getMainForm().removeEdge(l);
                }
              }

              slot.setType(type);
              updater.update();
              return value;
            }
          }

          return slot_types[slot.getType()];
        }
      });

      optType.setOption("Тип");
      optType.setValue(slot_types[slot.getType()]);
      grpSlot.add(optType);

      rm.addEditorForRow(index, 1, Utils.createCbCellRenderer(slot_types));
      ++index;
    }

    userData = new ArrayList<Object> ();
    userData.add(slot);

    if (slot.getType() == Slot.ENUM) {
      /* Slot domain */
      OptionEntry optDomain = new OptionEntry(new ValueSetter (slot) {
        @Override
        public Object setValue(Object value)
        {
          ISlot slot = (ISlot)userData;

          Domen domain = Frameset.getInstance().getDomenByName((String)value);
          Domen curDom = slot.getValue() != null ? slot.getValue().getDomen() : null;
          if (domain != curDom && domain != null && domain.getValues().get(0) != null) {
            slot.setValue(domain.getValues().get(0));
            updater.update();
          }

          return value;
        }
      });

      Domen domain = slot.getValue().getDomen();

      optDomain.setOption("Домен");
      optDomain.setValue(domain != null ? domain.getName() : "");
      grpSlot.add(optDomain);

      rm.addEditorForRow(index, 1, Utils.createCbCellRenderer(Frameset.getInstance().getDomainNames ()));
      ++index;

      /* Slot value */
      OptionEntry optValue = new OptionEntry(new ValueSetter (slot) {
        @Override
        public Object setValue(Object value)
        {
          ISlot slot = (ISlot)userData;

          Domen domain = slot.getValue() != null ? slot.getValue().getDomen() : null;
          if (domain != null) {
            slot.setValue(domain.getValueByName(value.toString()));
            updater.update();
            return value;
          }

          return slot.getValue();
        }
      });

      optValue.setOption("Значение");
      optValue.setValue(slot.getValue().toString());
      grpSlot.add(optValue);

      if (domain != null) {
        rm.addEditorForRow(index, 1, Utils.createCbCellRenderer(Utils.arraylist2strings(domain.getValues())));
      } else {
      }
      ++index;
    } else if (slot.getType() == Slot.SUBFRAME) {
      /* Slot sub-frame */
      OptionEntry optSubframe = new OptionEntry(new ValueSetter (slot) {
        @Override
        public Object setValue(Object value)
        {
          ISlot slot = (ISlot)userData;
          Link l = slot.getOwnInLink();
          Frame oldFrame = slot.getInLink() != null ? slot.getInLink().getSource() : null;

          Frame frame = Frameset.getInstance().getFrameByName((String)value);
          if (frame != null && frame != oldFrame && frame != slot.getParent()) {
            if (l != null) {
              Frameset.getInstance().removeLink(l);
              _System.getInstance().getMainForm().removeEdge(l);
            }

            if (slot.getInLink() != null) {
              if (frame == slot.getInLink().getSource()) {
                return value;
              }
            }

            try
            {
              l = Frameset.getInstance().createLink(frame, slot, Link.SUB_FRAME);
              _System.getInstance().getMainForm().insertEdge(frame, slot.getParent(), Link.SUB_FRAME, l);
              updater.update();
              return value;
            } catch (Exception e) {
              e.printStackTrace();
            }
          }

          return oldFrame != null ? oldFrame.getName() : "";
        }
      });

      optSubframe.setOption("Субфрейм");
      if (slot.getInLink() != null) {
        optSubframe.setValue(slot.getInLink().getSource().getName());
      } else {
        optSubframe.setValue("");
      }
      grpSlot.add(optSubframe);

      rm.addEditorForRow(index, 1, Utils.createCbCellRenderer(Utils.arraylist2strings(Frameset.getInstance().getAllFrames())));
      ++index;
    } else if (slot.getType() == Slot.IMAGE) {
      /* Slot image */
      OptionEntry optImage = new OptionEntry(new ValueSetter (slot) {
        @Override
        public Object setValue(Object value)
        {
          ISlot slot = (ISlot)userData;

          slot.setPathToImage((String)value);

          return value;
        }
      });

      optImage.setOption("Изображение");
      optImage.setValue(slot.getPathToImage());
      grpSlot.add(optImage);

      ++index;
    } else if (slot.getType() == Slot.PRODUCTIONAL) {
      OptionEntry optRules = new OptionEntry(new ValueSetter (slot) {
        @Override
        public Object setValue(Object value)
        {
          return "";
        }
      });

      optRules.setOption("Правила");
      grpSlot.add(optRules);

      rm.addEditorForRow(index, 1, Utils.createProductionalCellRenderer(slot));
      ++index;
    } else if (slot.getType() == Slot.TEXT) {
      /* Slot image */
      OptionEntry optText = new OptionEntry(new ValueSetter (slot) {
        @Override
        public Object setValue(Object value)
        {
          ISlot slot = (ISlot)userData;

          slot.setText((String) value);

          return value;
        }
      });

      optText.setOption("Текст");
      optText.setValue(slot.getText());
      grpSlot.add(optText);

      ++index;
    } 

    return index;
  }

  private void fill(RowEditorModel rm)
  {
    slotsParent = null;
    int index = 1;

    rm.removeAll();

    if (frame == null)
    {
      /* No options to be filled */
      return;
    }

    OptionEntry optName = new OptionEntry(new ValueSetter () {
      @Override
      public Object setValue(Object value)
      {
        frame.setName(((String)value).trim());
        return frame.getName();
      }
    });

    optName.setOption("Имя");
    optName.setValue(frame.getName());
    root.add(optName);
    ++index;

    List<ISlot> slots = frame.getSlots();
    if (!slots.isEmpty())
    {
      GroupEntry grpSlots = new GroupEntry("Слоты");
      slotsParent = grpSlots;
      root.add(grpSlots);
      ++index;

      for (ISlot slot : slots)
      {
        index = fillSlot(slot, grpSlots, rm, index);
      }
    }

    OptionEntry optComment = new OptionEntry(new ValueSetter () {
      @Override
      public Object setValue(Object value)
      {
        if (value == null)
          value = "";

        frame.setComment(((String)value).trim());
        return frame.getComment();
      }
    });

    optComment.setOption("Комментарий");
    optComment.setValue(frame.getComment());
    root.add(optComment);
    ++index;
  }

  public Options(Frame frame, RowEditorModel rm, OptionsUpdater updater)
  {
    this.updater = updater;

    this.frame = frame;
    root = new OptionEntry();
    root.setOption("Options");
    fill(rm);
  }

  public OptionEntry getRoot()
  {
    return root;
  }

  public class ValueSetter
  {
    protected Object userData;

    public ValueSetter()
    {
      
    }

    public ValueSetter(Object userData)
    {
      this.userData = userData;
    }

    public Object setValue (Object value)
    {
      return value;
    }
  }

  public class GroupEntry extends DefaultMutableTreeNode
  {
    private Object tag;

    public GroupEntry(String name)
    {
      super(name);
    }

    public GroupEntry(String name, Object tag)
    {
      super(name);
      this.tag = tag;
    }


    public void setTag(Object tag)
    {
      this.tag = tag;
    }

    public Object getTag()
    {
      return tag;
    }

    public String GetName()
    {
      return (String)getUserObject();
    }

    public void SetName(String name)
    {
      setUserObject(name);
    }

  }

  public class OptionEntry extends DefaultMutableTreeNode
  {

    private String option;
    private Object value;
    private ValueSetter setter;

    public OptionEntry()
    {
      setter = null;
    }

    public OptionEntry(ValueSetter setter)
    {
      this.setter = setter;
    }

    public void setOption(String option)
    {
      this.option = option;
      setUserObject(option);
    }

    public String getOption()
    {
      return option;
    }

    public void setValue(Object value)
    {
      if (setter != null)
      {
        value = setter.setValue(value);
      }

      this.value = value;
    }

    public Object getValue()
    {
      return value;
    }
  }
}
