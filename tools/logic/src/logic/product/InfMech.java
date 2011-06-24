package logic.product;

//package Core.Logic.Product;
//
//import Core.Logic.Frames.Frame;
//import Core.Logic.Frames.ISlot;
////import UI.Forms.QuestionDialog.QuestionDialog;
//import java.util.ArrayList;
//import java.util.HashMap;
//import javax.swing.tree.DefaultMutableTreeNode;
////import frameEditor._System;
//
//public class InfMech
//{
//
//  private ArrayList<Object> smallExplanation = null;
//  private ISlot goalSlot = null;
//  private HashMap<String, String> data;
//  private String path;
//
//  public InfMech()
//  {
//    smallExplanation = new ArrayList<Object>();
//  }
//
//  public boolean start(ISlot goalSlot, ArrayList<Rule> rules, HashMap<String, String> data, String path, DefaultMutableTreeNode root)
//  {
//    this.goalSlot = goalSlot;
//    this.data = data;
//    this.path = path;
//
//    if (Revers(goalSlot, rules, data, path, root))
//      return data.get(path) != null;
//
//    return false;
//  }
//
//  private ArrayList<Rule> getActiveRules(ISlot goalSlot, ArrayList<Rule> rules)
//  {
//    ArrayList<Rule> currentRules = new ArrayList<Rule>();
//    Frame goalFrame = goalSlot.getParent();
//
//    if(rules == null)
//      return currentRules;
//
//    for (Rule r : rules)
//    {
//      ArrayList<Pair> pairs = r.getThenPart();
//      for (Pair p : pairs)
//      {
//        if (goalFrame.isInstanceOf(p.getFrame()) && p.getSlot().getName().equals(goalSlot.getName()))
//        {
//          currentRules.add(r);
//        }
//      }
//    }
//
//    return currentRules;
//  }
//
//  private boolean slotIsKnown(ISlot slot)
//  {
//    if (slot == this.goalSlot)
//      return data.get(path) != null;
//
//    return !(slot.getValue() == null || slot.getValue().getValue() == null || slot.getValue().getValue().equals("Неизвестно"));
//  }
//
//  // TODO refactoring
//  /**
//   * Обратый вывод. Написан так, что черт ногу сломит.
//   */
//  private boolean Revers(ISlot goalSlot, ArrayList<Rule> allRules, HashMap<String, String> data, String path, DefaultMutableTreeNode node)
//  {
//    Frame goalFrame = goalSlot.getParent();
//    String slotName = goalFrame.getName() + "." + goalSlot.getName();
//    DefaultMutableTreeNode dmtn = new DefaultMutableTreeNode("Выводим '" + slotName + "'");
//    node.add(dmtn);
//    if (this.goalSlot == goalSlot || !slotIsKnown(goalSlot))
//    {
//      int type;
//
//      // XXX: slot does not store variable type atm
//      type = Variable.DERIVABLE_TYPE;
//
//      switch (type)
//      {
//        case Variable.DERIVABLE_TYPE:
//          ArrayList<Rule> rules = getActiveRules(goalSlot, allRules);
//
//          if (rules.isEmpty())
//          {
//            dmtn.add(new DefaultMutableTreeNode("Нет правил"));
//          }
//
//          for (Rule r : rules)
//          {
//            boolean isTrue = true;
//            DefaultMutableTreeNode dmtn1 = new DefaultMutableTreeNode(r.getName() + " : " + r.getText());
//            dmtn.add(dmtn1);
//            for (Pair p : r.getIfPart())
//            {
//              if (isTrue)
//              {
//                ISlot slot = p.getSlot();
//                if (!Revers(slot, allRules, data, path, dmtn1))
//                {
//                  return false;
//                }
//
//                if (slot.getValue() == null || slot.getValue().getValue() == null ||
//                        (!slot.getValue().getValue().equals(p.getValue().getValue()) &&
//                         !slot.getValue().getValue().equals("Неважно")))
//                {
//                  isTrue = isTrue && false;
//                }
//              }
//            }
//            if (isTrue)
//            {
//              smallExplanation.add(r.getExplanation());
//              dmtn1.add(new DefaultMutableTreeNode("Правило сработало"));
//
//              for (Pair p : r.getThenPart())
//              {
//                //p.getSlot().setValue(new Value(p.getValue().getValue()));
//                data.put(path, p.getValue().getValue());
//              }
//
//            } else
//            {
//              dmtn1.add(new DefaultMutableTreeNode("Правило не сработало"));
//            }
//
//            if (slotIsKnown(goalSlot))
//            {
//              String val =goalSlot.getValue().getValue();
//
//              if(goalSlot == this.goalSlot)
//                val = data.get(path);
//
//              dmtn.add(new DefaultMutableTreeNode("Вывели: '" + slotName + "' = '" + val + "'"));
//              return true;
//            }
//          }
//          if (slotIsKnown(goalSlot))
//          {
//            dmtn.add(new DefaultMutableTreeNode("Вывели: '" + slotName + "' = '" + goalSlot.getValue().getValue() + "'"));
//          } else
//          {
//            dmtn.add(new DefaultMutableTreeNode("Не вывели"));
//          }
//          break;
//        case Variable.REQUERED_TYPE:
////          QuestionDialog qd = new QuestionDialog(_System.getInstance().getMainForm(), true);
//          qd.run(goalSlot);
//          if (qd.getResult() == 0 || goalSlot.getValue() == null)
//          {
//            return false;
//          } else
//          {
//            dmtn.add(new DefaultMutableTreeNode("Пользователь означил : " + "'" + slotName + "' = '" + goalSlot.getValue().getValue() + "'"));
//          }
//          break;
//        case Variable.DERIVABLE_REQUERED_TYPE:
//          rules = getActiveRules(goalSlot, allRules);
//
//          if (rules.isEmpty())
//          {
//            dmtn.add(new DefaultMutableTreeNode("Нет правил"));
//          }
//
//          for (Rule r : rules)
//          {
//            boolean isTrue = true;
//            DefaultMutableTreeNode dmtn1 = new DefaultMutableTreeNode(r.getName() + " : " + r.getText());
//            dmtn.add(dmtn1);
//            for (Pair p : r.getIfPart())
//            {
//              ISlot slot = p.getSlot();
//              if (!Revers(slot, allRules, data, path, dmtn1))
//              {
//                return false;
//              }
//
//              if (slot.getValue() == null || slot.getValue().getValue() == null ||
//                      !slot.getValue().getValue().equals(p.getValue().getValue()))
//              {
//                isTrue = isTrue && false;
//              }
//            }
//            if (isTrue)
//            {
//              smallExplanation.add(r.getExplanation());
//              dmtn1.add(new DefaultMutableTreeNode("Правило сработало"));
//
//              for (Pair p : r.getThenPart())
//              {
//                p.getSlot().setValue(new Value(p.getValue().getValue()));
//              }
//
//            } else
//            {
//              dmtn1.add(new DefaultMutableTreeNode("Правило не сработало"));
//            }
//
//            if (slotIsKnown(goalSlot))
//            {
//              dmtn.add(new DefaultMutableTreeNode("Вывели: '" + slotName + "' = '" + goalSlot.getValue().getValue() + "'"));
//              return true;
//            }
//          }
//          if (slotIsKnown(goalSlot))
//          {
//            dmtn.add(new DefaultMutableTreeNode("Вывели: '" + slotName + "' = '" + goalSlot.getValue().getValue() + "'"));
//          } else
//          {
//            dmtn.add(new DefaultMutableTreeNode("Не вывели"));
////            qd = new QuestionDialog(_System.getInstance().getMainForm(), true);
////            qd.run(goalSlot);
////            if (qd.getResult() == 0 || goalSlot.getValue() == null)
//            {
//              return false;
//            } else
//            {
//              dmtn.add(new DefaultMutableTreeNode("Пользователь означил : " + "'" + slotName + "' = '" + goalSlot.getValue().getValue() + "'"));
//            }
//          }
//          break;
//      }
//      return true;
//    } else
//    {
//      dmtn.add(new DefaultMutableTreeNode("Уже: '" + slotName + "' = '" + goalSlot.getValue().getValue() + "'"));
//      return true;
//    }
//  }
//
//  public ArrayList<Object> getSmallExplanation()
//  {
//    return smallExplanation;
//  }
//}
