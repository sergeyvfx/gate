package UI;

import logic.product.Value;
import logic.frames.Frameset;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.Window;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.ArrayList;
import java.util.HashMap;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JComboBox;
import javax.swing.JDialog;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class VehicleDialog extends JDialog
{

  private JComboBox jcbType;
  private JComboBox jcbMaxMass;
  private JComboBox jcbPlaces;
  private JComboBox jcbEngine;
  private JComboBox jcbDirection;
  private JComboBox jcbSignals;
  private JButton jbOk;
  private JButton jbCancel;
  private int result;

  public VehicleDialog(Window parent)
  {
    super(parent);
    createUI();
  }

  private Object[] getArrayWithoutUnknownValue(ArrayList<Value> values)
  {
    ArrayList<Value> res = new ArrayList<Value>();
    for (Value v : values)
    {
      if (!v.getValue().equals("Неизвестно"))
      {
        res.add(v);
      }
    }
    return res.toArray();
  }

  private void createUI()
  {
    setLayout(new GridBagLayout());
    setTitle("Заполнение полей");
    setModal(true);
    setDefaultCloseOperation(JDialog.DISPOSE_ON_CLOSE);

    jbOk = new JButton("ОК", new ImageIcon(getClass().getResource("/Images/16x16/apply.png")));
    jbOk.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent e)
      {
        result = 0;
        dispose();
      }
    });
    jbCancel = new JButton("Отмена", new ImageIcon(getClass().getResource("/Images/16x16/cancel.png")));
    jbCancel.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent e)
      {
        result = -1;
        dispose();
      }
    });
    jcbType = new JComboBox(getArrayWithoutUnknownValue(Frameset.getInstance().getDomenByName("Тип ТС").getValues()));
    jcbMaxMass = new JComboBox(getArrayWithoutUnknownValue(Frameset.getInstance().getDomenByName("Разрешенная максимальная масса").getValues()));
    jcbPlaces = new JComboBox(getArrayWithoutUnknownValue(Frameset.getInstance().getDomenByName("Кол-во посадочных мест").getValues()));
    jcbEngine = new JComboBox(getArrayWithoutUnknownValue(Frameset.getInstance().getDomenByName("Объем двигателя").getValues()));
    jcbDirection = new JComboBox(getArrayWithoutUnknownValue(Frameset.getInstance().getDomenByName("Направление").getValues()));
    jcbSignals = new JComboBox(getArrayWithoutUnknownValue(Frameset.getInstance().getDomenByName("Логический").getValues()));

    add(new JLabel("Тип:"), new GridBagConstraints(0, 0, 1, 1, 0, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.NONE, new Insets(5, 5, 0, 5), 0, 0));
    add(jcbType, new GridBagConstraints(0, 1, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Разрешенная максимальная масса:"), new GridBagConstraints(0, 2, 1, 1, 0, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.NONE, new Insets(5, 5, 0, 5), 0, 0));
    add(jcbMaxMass, new GridBagConstraints(0, 3, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Кол-во посадочных мест:"), new GridBagConstraints(0, 4, 1, 1, 0, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.NONE, new Insets(5, 5, 0, 5), 0, 0));
    add(jcbPlaces, new GridBagConstraints(0, 5, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Двигатель:"), new GridBagConstraints(0, 6, 1, 1, 0, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.NONE, new Insets(5, 5, 0, 5), 0, 0));
    add(jcbEngine, new GridBagConstraints(0, 7, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Направление:"), new GridBagConstraints(0, 8, 1, 1, 0, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.NONE, new Insets(5, 5, 0, 5), 0, 0));
    add(jcbDirection, new GridBagConstraints(0, 9, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Наличие спецсигналов:"), new GridBagConstraints(0, 10, 1, 1, 0, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.NONE, new Insets(5, 5, 0, 5), 0, 0));
    add(jcbSignals, new GridBagConstraints(0, 11, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    JPanel jPanel = new JPanel(new GridBagLayout());
    jPanel.add(jbOk, new GridBagConstraints(0, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    jPanel.add(jbCancel, new GridBagConstraints(1, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(jPanel, new GridBagConstraints(0, 12, 2, 1, 0, 1,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(0, 0, 0, 0), 0, 0));
    pack();
    this.setLocation((this.getParent().getWidth() - this.getWidth()) / 2
            + this.getParent().getX(), (this.getParent().getHeight()
            - this.getHeight()) / 2 + this.getParent().getY());
  }

  public HashMap<String, String> run()
  {
    jcbSignals.setSelectedIndex(1);
    setVisible(true);
    if (result == 0)
    {
      HashMap<String, String> hm = new HashMap<String, String>();
      hm.put("Тип", ((Value) jcbType.getSelectedItem()).getValue());
      hm.put("Разрешенная максимальная масса", ((Value) jcbMaxMass.getSelectedItem()).getValue());
      hm.put("Кол-во посадочных мест", ((Value) jcbPlaces.getSelectedItem()).getValue());
      hm.put("Направление движения", ((Value) jcbDirection.getSelectedItem()).getValue());
      hm.put("Двигатель", ((Value) jcbEngine.getSelectedItem()).getValue());
      hm.put("Наличие спецсигналов", ((Value) jcbSignals.getSelectedItem()).getValue());
      return hm;
    } else
    {
      return null;
    }
  }
}
