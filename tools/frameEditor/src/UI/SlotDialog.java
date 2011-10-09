package UI;

import java.awt.event.KeyEvent;
import logic.product.Domen;
import logic.product.Rule;
import logic.product.Value;
import logic.frames.Frame;
import logic.frames.Frameset;
import logic.frames.ISlot;
import logic.frames.Link;
import logic.frames.Slot;
import UI.Forms.Domen.ChangeDomenDialog;
import UI.Forms.Rule.RulePanel;
import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ItemEvent;
import java.awt.event.ItemListener;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.awt.image.FilteredImageSource;
import java.awt.image.ImageFilter;
import java.awt.image.ImageProducer;
import java.awt.image.ReplicateScaleFilter;
import java.io.File;
import java.util.ArrayList;
import java.util.List;
import javax.swing.BorderFactory;
import javax.swing.DefaultComboBoxModel;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JComboBox;
import javax.swing.JFileChooser;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTabbedPane;
import javax.swing.JTextField;
import javax.swing.filechooser.FileNameExtensionFilter;
import frameEditor._System;
import java.awt.event.KeyListener;

/**
 *
 * @author nazgul
 */
public class SlotDialog extends javax.swing.JDialog
{

  protected ISlot editSlot;
  private String slotName = "";
  private Value value = null;
  private Frame frame = null;
  private int slotType = Slot.ENUM;
  private Frame curFrame = null;
  private JButton jBtnCancel;
  private JButton jBtnOk;
  private JButton jButtonBrowse;
  private JButton jButtonDomen;
  private JButton jButtonValue;
  private JComboBox jCbDomen;
  private JComboBox jCbFrames;
  private JComboBox jCbType;
  private JComboBox jCbValue;
  private JPanel jDefValPanel;
  private JPanel jFramelinkPanel;
  private JPanel jTextPanel;
  private JPanel jPanelSlotsProperty;
  private JPanel jImagePanel;
  private RulePanel jRulePanel;
  private JPanel buttonPanel;
  private JTextField jEdtSlotName;
  private JTextField jText;
  private JTextField jTextFieldPathToImage;
  private JLabel jLabelImage;
  private JTabbedPane jTabbedPanel;

  private void fillFrames()
  {
    DefaultComboBoxModel model = (DefaultComboBoxModel) jCbFrames.getModel();
    model.removeAllElements();

    model.addElement(null);
    List<Frame> frames = Frameset.getInstance().getAllFrames();
    for (Frame f : frames)
    {
      if (curFrame != f)
      {
        model.addElement(f);
      }
    }
  }

  private void updateForm()
  {
    int type = jCbType.getSelectedIndex();
    if (Slot.ENUM == type)
    {
      jDefValPanel.setVisible(true);
      jFramelinkPanel.setVisible(false);
      jRulePanel.setVisible(false);
      jImagePanel.setVisible(false);
      jTextPanel.setVisible(false);
    } else if (Slot.SUBFRAME == type)
    {
      jDefValPanel.setVisible(false);
      jFramelinkPanel.setVisible(true);
      jRulePanel.setVisible(false);
      jImagePanel.setVisible(false);
      jTextPanel.setVisible(false);
    } else if (Slot.PRODUCTIONAL == type)
    {
      jDefValPanel.setVisible(false);
      jFramelinkPanel.setVisible(false);
      jRulePanel.setVisible(true);
      jImagePanel.setVisible(false);
      jTextPanel.setVisible(false);
    } else if (Slot.IMAGE == type)
    {
      jDefValPanel.setVisible(false);
      jFramelinkPanel.setVisible(false);
      jRulePanel.setVisible(false);
      jImagePanel.setVisible(true);
      jTextPanel.setVisible(false);
      updateJLabelImage();
    } else if (Slot.TEXT == type)
    {
      jDefValPanel.setVisible(false);
      jFramelinkPanel.setVisible(false);
      jRulePanel.setVisible(false);
      jImagePanel.setVisible(false);
      jTextPanel.setVisible(true);
      updateJLabelImage();
    }
    setMinimumSize(new Dimension(0, 0));
    pack();
    setMinimumSize(getSize());
    this.setLocation((this.getParent().getWidth() - this.getWidth()) / 2
            + this.getParent().getX(), (this.getParent().getHeight()
            - this.getHeight()) / 2 + this.getParent().getY());
  }

  /**
   * Init my own components
   */
  private void myInitComponents()
  {
    fillFrames();
    jCbDomen.setModel(new DefaultComboBoxModel(Frameset.getInstance().getDomens().toArray()));
    jEdtSlotName.setText(slotName);
    jCbType.setSelectedIndex(slotType);
    switch (slotType)
    {
      case Slot.ENUM:
        if (value != null)
        {
          jCbDomen.setSelectedItem(value.getDomen());
          updatejCbValue();
          jCbValue.setSelectedItem(value);
        } else
        {
          updatejCbValue();
          jCbDomen.setSelectedItem(null);
          jCbValue.setSelectedItem(null);
        }
        break;
      case Slot.SUBFRAME:
        jCbFrames.setSelectedItem(frame);
        break;
    }
    updateForm();
  }

  /** Creates new form SlotDialog */
  public SlotDialog(java.awt.Frame parent, boolean modal, ISlot editSlot, boolean isOwnSlot)
  {
    super(parent, modal);
    this.editSlot = editSlot;
    createUI();
    if (editSlot != null)
    {
      setTitle("Edit slot");
      this.slotName = editSlot.getName();
      this.slotType = editSlot.getType();
      switch (slotType)
      {
        case Slot.ENUM:
          this.value = editSlot.getValue();
          break;
        case Slot.SUBFRAME:
          Link l = editSlot.getInLink();
          if (l != null)
          {
            this.frame = l.getSource();
          } else
          {
            this.frame = null;
          }
          break;
        case Slot.PRODUCTIONAL:
          jRulePanel.setRules(new ArrayList<Rule>(editSlot.getRules()));
          jRulePanel.setGoalSlot(editSlot.getGoalSlot());
          break;
        case Slot.IMAGE:
          jTextFieldPathToImage.setText(editSlot.getPathToImage());
          break;
        case Slot.TEXT:
          jText.setText(editSlot.getText());
          break;
      }
    } else
    {
      setTitle("Create slot");
    }
    jEdtSlotName.setEnabled(isOwnSlot);
    jCbType.setEnabled(isOwnSlot);
    this.curFrame = Frameset.getInstance().getActiveFrame();
    myInitComponents();
  }

  /**
   * Create SlotDialog with editSlot == null
   * @param parent
   * @param frame
   * @param curFrame
   */
  public SlotDialog(java.awt.Frame parent, Frame frame, Frame curFrame)
  {
    super(parent, true);
    createUI();
    this.slotType = Slot.SUBFRAME;
    this.frame = frame;
    this.curFrame = curFrame;
    myInitComponents();
  }

  private void createUI()
  {
    this.addKeyListener(new KeyListener() {

      @Override
      public void keyTyped(KeyEvent e)
      {
      }

      @Override
      public void keyPressed(KeyEvent e)
      {
        switch (e.getKeyCode()) {
          case KeyEvent.VK_ENTER:
            jBtnOkActionPerformed(null);
            break;
          case KeyEvent.VK_ESCAPE:
            jBtnCancelActionPerformed(null);
            break;
        }
      }

      @Override
      public void keyReleased(KeyEvent e)
      {
      }
    });
    // Определяем вкладку
    jTabbedPanel = new JTabbedPane();
    jPanelSlotsProperty = new JPanel(new GridBagLayout());
    jTabbedPanel.add("Свойства слота", jPanelSlotsProperty);

    // На вкладке всегд аотображается имя и тип слота
    JPanel general = new JPanel(new GridBagLayout());
    general.setBorder(BorderFactory.createTitledBorder("Общая информация"));
    general.add(new JLabel("Имя слота:"), new GridBagConstraints(0, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jEdtSlotName = new JTextField(40);
    general.add(jEdtSlotName, new GridBagConstraints(1, 0, 1, 1, 2, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    general.add(new JLabel("Тип:"), new GridBagConstraints(2, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jCbType = new JComboBox(new DefaultComboBoxModel(new String[]
            {
              "Перечислимый", "Sub-фрейм", "Продукционный", "Изображение", "Текст"
            }));
    jCbType.addItemListener(new ItemListener()
    {

      @Override
      public void itemStateChanged(ItemEvent e)
      {
        updateForm();
      }
    });

    general.add(jCbType, new GridBagConstraints(3, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));

    // Теперь часть формы зависящая от типа слота
    // Для продукционного слота
    jRulePanel = new RulePanel((java.awt.Frame) getParent(), null);

    // Для sub-фрейма
    jFramelinkPanel = new JPanel(new GridBagLayout());
    jFramelinkPanel.setBorder(BorderFactory.createTitledBorder("Связь с фреймом"));
    jFramelinkPanel.add(new JLabel("Выберите фрейм:"), new GridBagConstraints(
            0, 0, 1, 1, 0, 0, GridBagConstraints.BASELINE,
            GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jCbFrames = new JComboBox();
    jFramelinkPanel.add(jCbFrames, new GridBagConstraints(1, 0, 1, 1, 1, 1,
            GridBagConstraints.BASELINE, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    
    // Для текста
    jTextPanel = new JPanel(new GridBagLayout());
    jTextPanel.setBorder(BorderFactory.createTitledBorder("Текст"));
    jTextPanel.add(new JLabel("Введите значение:"), new GridBagConstraints(
            0, 0, 1, 1, 0, 0, GridBagConstraints.BASELINE,
            GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jText = new JTextField();
    jTextPanel.add(jText, new GridBagConstraints(1, 0, 1, 1, 1, 1,
            GridBagConstraints.BASELINE, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));

    // Для изображения
    jImagePanel = new JPanel(new GridBagLayout());
    jImagePanel.setBorder(BorderFactory.createTitledBorder("Изображение"));
    jLabelImage = new JLabel();
    jLabelImage.setHorizontalAlignment(javax.swing.SwingConstants.CENTER);
    jLabelImage.setBorder(BorderFactory.createEtchedBorder());
//    jLabelImage.setMaximumSize(new java.awt.Dimension(320, 240));
//    jLabelImage.setMinimumSize(new java.awt.Dimension(320, 240));
    jLabelImage.setPreferredSize(new java.awt.Dimension(320, 240));
    jTextFieldPathToImage = new JTextField(45);
    jButtonBrowse = new JButton("Обзор...");
    jButtonBrowse.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent evt)
      {
        jButtonBrowseActionPerformed(evt);
      }
    });
    jImagePanel.add(new JLabel("Путь к файлу:"), new GridBagConstraints(0, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jImagePanel.add(jTextFieldPathToImage, new GridBagConstraints(1, 0, 1, 1, 1, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    jImagePanel.add(jButtonBrowse, new GridBagConstraints(2, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jImagePanel.add(jLabelImage, new GridBagConstraints(0, 1, 3, 1, 1, 1,
            GridBagConstraints.NORTHWEST, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));

    // Для значения по умолчанию
    jDefValPanel = new JPanel(new GridBagLayout());
    jDefValPanel.setBorder(BorderFactory.createTitledBorder("Значение по умолчанию"));

    jCbValue = new JComboBox();
    jButtonValue = new JButton(new ImageIcon(getClass().getResource("/Images/16x16/more.png")));
    jButtonValue.setMaximumSize(new java.awt.Dimension(26, 26));
    jButtonValue.setMinimumSize(new java.awt.Dimension(26, 26));
    jButtonValue.setPreferredSize(new java.awt.Dimension(26, 26));
    jButtonValue.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent evt)
      {
        jButtonValueActionPerformed(evt);
      }
    });

    jCbDomen = new JComboBox();
    jCbDomen.addItemListener(new ItemListener()
    {

      @Override
      public void itemStateChanged(ItemEvent evt)
      {
        jCbDomenItemStateChanged(evt);
      }
    });

    jButtonDomen = new JButton(new ImageIcon(getClass().getResource("/Images/16x16/more.png")));
    jButtonDomen.setMaximumSize(new java.awt.Dimension(26, 26));
    jButtonDomen.setMinimumSize(new java.awt.Dimension(26, 26));
    jButtonDomen.setPreferredSize(new java.awt.Dimension(26, 26));
    jButtonDomen.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent evt)
      {
        jButtonDomenActionPerformed(evt);
      }
    });

    jDefValPanel.add(new JLabel("Домен:"), new GridBagConstraints(0, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jDefValPanel.add(jCbDomen, new GridBagConstraints(1, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    jDefValPanel.add(jButtonDomen, new GridBagConstraints(2, 0, 1, 1, 0, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jDefValPanel.add(new JLabel("Значение:"), new GridBagConstraints(0, 1, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    jDefValPanel.add(jCbValue, new GridBagConstraints(1, 1, 1, 1, 1, 1,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    jDefValPanel.add(jButtonValue, new GridBagConstraints(2, 1, 1, 1, 0, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));

    jPanelSlotsProperty.add(general, new GridBagConstraints(0, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    jPanelSlotsProperty.add(jDefValPanel, new GridBagConstraints(0, 1, 1, 1, 1, 1,
            GridBagConstraints.NORTH, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    jPanelSlotsProperty.add(jRulePanel, new GridBagConstraints(0, 2, 1, 1, 1, 1,
            GridBagConstraints.NORTH, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    jPanelSlotsProperty.add(jFramelinkPanel, new GridBagConstraints(0, 3, 1, 1, 1, 1,
            GridBagConstraints.NORTH, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    jPanelSlotsProperty.add(jImagePanel, new GridBagConstraints(0, 4, 1, 1, 1, 1,
            GridBagConstraints.NORTH, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    jPanelSlotsProperty.add(jTextPanel, new GridBagConstraints(0, 5, 1, 1, 1, 1,
            GridBagConstraints.NORTH, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));

    // И напоследок кнопки
    jBtnOk = new JButton("ОK", new ImageIcon(getClass().getResource("/Images/16x16/apply.png")));
    jBtnOk.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent evt)
      {
        jBtnOkActionPerformed(evt);
      }
    });

    jBtnCancel = new JButton("Отмена", new ImageIcon(getClass().getResource("/Images/16x16/cancel.png")));
    jBtnCancel.addActionListener(new ActionListener()
    {

      @Override
      public void actionPerformed(ActionEvent evt)
      {
        jBtnCancelActionPerformed(evt);
      }
    });

    buttonPanel = new JPanel(new GridBagLayout());
    buttonPanel.add(jBtnOk, new GridBagConstraints(0, 0, 1, 1, 1, 0,
            GridBagConstraints.CENTER, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    buttonPanel.add(jBtnCancel, new GridBagConstraints(1, 0, 1, 1, 1, 0,
            GridBagConstraints.CENTER, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    getContentPane().setLayout(new GridBagLayout());
    getContentPane().add(jTabbedPanel, new GridBagConstraints(0, 0, 1, 1, 1, 1,
            GridBagConstraints.NORTHWEST, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    getContentPane().add(buttonPanel, new GridBagConstraints(0, 1, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
  
    pack();
    setDefaultCloseOperation(DISPOSE_ON_CLOSE);
    addWindowListener(new WindowAdapter()
    {

      @Override
      public void windowOpened(WindowEvent evt)
      {
        formWindowOpened(evt);
      }
    });
  }

  private boolean checkAndHandle()
  {
    String name = jEdtSlotName.getText().trim();

    /* Проверка на ошибки */
    // Сначала общие ошибки для всех типов слотов: пустое имя
    if (name.length() == 0)
    {
      showError("Имя слота не может быть пустым");
      jEdtSlotName.requestFocus();
      return false;
    }

    // и существующее имя
    Slot slotByName;
    if (editSlot != null)
    {
      slotByName = editSlot.getParent().getOwnSlotByName(name);
    } else
    {
      slotByName = curFrame.getOwnSlotByName(name);
    }

    if ((editSlot == null && slotByName != null)
            || (editSlot != null && slotByName != null && slotByName != editSlot))
    {
      showError("Слот с таким именем уже существует");
      jEdtSlotName.requestFocus();
      return false;
    }

    // Затем проверим на специфические ошибки
    switch (jCbType.getSelectedIndex())
    {
      case Slot.ENUM:
        Domen d = (Domen) jCbDomen.getSelectedItem();
        Value v;
        if (d != null)
        {
          v = (Value) jCbValue.getSelectedItem();
          if (v == null)
          {
            showError("Выберите значение");
            return false;
          }
        } else
        {
          showError("Выберите домен");
          return false;
        }
        break;
      case Slot.SUBFRAME:
        break;
      case Slot.PRODUCTIONAL:
        break;
      case Slot.IMAGE:
        File f = new File(jTextFieldPathToImage.getText());
        if (!f.exists())
        {
          showError("Выбранный файл не уществует");
          return false;
        }
        break;
      case Slot.TEXT:
        break;
    }

    ISlot curSlot;
    if (editSlot != null)
    {
      editSlot.setName(name);
      curSlot = editSlot;
    } else
    {
      curSlot = curFrame.createSlot(name);
    }

    curSlot.setType(jCbType.getSelectedIndex());
    switch (curSlot.getType())
    {
      case Slot.ENUM:
        curSlot.setValue((Value) jCbValue.getSelectedItem());
        break;
      case Slot.SUBFRAME:
        Frame frameLink = (Frame) jCbFrames.getSelectedItem();
        Link l = curSlot.getInLink();
        //Если существует входящая связь
        if (l != null)
        {
          if (l.getSource() == frameLink)
          {
            // Если мы нашли такую связь, которая ведет к слоту
            // который мы редактируем
            // значит мы нифига не редактировали, а только мозги ...ли.
            return true;
          } else
          {
            // Иначе мы нашли входящую связь, которая ведет "налево"
            // Грохнем ее
            Frameset.getInstance().removeLink(l);
            _System.getInstance().getMainForm().removeEdge(l);
          }
        }
        //А теперь просто добавляем новую связь
        if (frameLink != null)
        {
          try
          {
            l = Frameset.getInstance().createLink(frameLink, curSlot,
                    Link.SUB_FRAME);
            _System.getInstance().getMainForm().insertEdge(frameLink,
                    curSlot.getParent(), Link.SUB_FRAME, l);
          } catch (Exception e)
          {
            e.printStackTrace();
          }
        }
        break;
      case Slot.PRODUCTIONAL:
        curSlot.setRules(jRulePanel.getRules());
        curSlot.setGoalSlot(jRulePanel.getGoalSlot());
        break;
      case Slot.IMAGE:
        curSlot.setPathToImage(jTextFieldPathToImage.getText());
        break;
      case Slot.TEXT:
        curSlot.setText(jText.getText());
        break;
    }

    return true;
  }

  private void jBtnCancelActionPerformed(java.awt.event.ActionEvent evt)
  {
    this.setVisible(false);
  }

  private void jBtnOkActionPerformed(java.awt.event.ActionEvent evt)
  {
    if (checkAndHandle())
    {
      this.setVisible(false);
      _System.getInstance().getMainForm().setIsChange(true);
    }
  }

  private void formWindowOpened(java.awt.event.WindowEvent evt)
  {
    jEdtSlotName.requestFocus();
  }

  private void jCbDomenItemStateChanged(java.awt.event.ItemEvent evt)
  {
    updatejCbValue();
  }

  private void jButtonDomenActionPerformed(java.awt.event.ActionEvent evt)
  {
    ChangeDomenDialog dialog = new ChangeDomenDialog((java.awt.Frame) getParent(), true);
    dialog.run((Domen) jCbDomen.getSelectedItem(), (Value) jCbValue.getSelectedItem());
    DefaultComboBoxModel model = new DefaultComboBoxModel(Frameset.getInstance().getDomens().toArray());
    jCbDomen.setModel(model);
    jCbDomen.setSelectedItem(dialog.getDomen());
    updatejCbValue();
    jCbValue.setSelectedItem(dialog.getValue());
  }

  private void jButtonValueActionPerformed(java.awt.event.ActionEvent evt)
  {
    jButtonDomenActionPerformed(evt);
  }

  private void jButtonBrowseActionPerformed(java.awt.event.ActionEvent evt)
  {
    JFileChooser jfc = new JFileChooser("./kb");
    jfc.setFileSelectionMode(JFileChooser.FILES_ONLY);
    jfc.setMultiSelectionEnabled(false);
    jfc.setFileFilter(new FileNameExtensionFilter("Images", "jpg"));
    if (jfc.showOpenDialog(this) == JFileChooser.APPROVE_OPTION)
    {
      File f = jfc.getSelectedFile();
      String relative = new File(".").toURI().relativize(f.toURI()).getPath();
      jTextFieldPathToImage.setText(relative);
    }
    updateJLabelImage();
  }

  private void updateJLabelImage()
  {
    ImageIcon ii = new ImageIcon(jTextFieldPathToImage.getText());
    if (ii.getIconHeight() <= 0 || ii.getIconWidth() <= 0)
    {
      ii = new ImageIcon(getClass().getResource("/Images/no_image.png"));
    }
    Double width = Double.parseDouble(Integer.toString(ii.getIconWidth()));
    Double height = Double.parseDouble(Integer.toString(ii.getIconHeight()));
    if (width > height)
    {
      width -= 5.0;
      height = Double.parseDouble(Integer.toString(jLabelImage.getWidth()))
              / width * height;
      width = Double.parseDouble(Integer.toString(jLabelImage.getWidth()));
    } else
    {
      height -= 5.0;
      width = Double.parseDouble(Integer.toString(jLabelImage.getHeight()))
              / height * width;
      height = Double.parseDouble(Integer.toString(jLabelImage.getHeight()));
    }

    ImageFilter replicate = new ReplicateScaleFilter(width.intValue(),
            height.intValue());
    ImageProducer prod = new FilteredImageSource(ii.getImage().getSource(),
            replicate);
    jLabelImage.setIcon(new ImageIcon(createImage(prod)));
  }

  private void updatejCbValue()
  {
    Domen domen = (Domen) jCbDomen.getSelectedItem();
    if (domen != null)
    {
      jCbValue.setModel(new DefaultComboBoxModel(domen.getValues().toArray()));
    } else
    {
      jCbValue.setModel(new DefaultComboBoxModel());
    }
  }

  private void showError(String msg)
  {
    JOptionPane.showMessageDialog(this, msg, "Ошибка",
            JOptionPane.ERROR_MESSAGE);
  }
}
