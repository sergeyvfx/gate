package frameEditor;

import UI.MainForm;
import javax.swing.JFrame;
import javax.swing.JOptionPane;

public class _System
{
  protected static _System instance = null;
  protected Events events;
  protected UI.MainForm mainForm;
  protected Outliner outliner;

  /**
   * Constructor
   */
  protected _System()
  {
    events = new Events();
    mainForm = new UI.MainForm();
    mainForm.setExtendedState(mainForm.getExtendedState() | JFrame.MAXIMIZED_BOTH);
    outliner = new Outliner(mainForm.getOutlinerTree(), events);
  }

  /**
   * Get instance of system
   *
   * @return system's instance
   */
  public static _System getInstance()
  {
    if (instance == null)
    {
      instance = new _System();
    }

    return instance;
  }

  /**
   * Get events
   *
   * @return system's events
   */
  public Events getEvents()
  {
    return events;
  }

  public void start()
  {
    mainForm.setVisible(true);
  }

  public MainForm getMainForm()
  {
    return mainForm;
  }

  public void showErrorMsg(String msg)
  {
    JOptionPane.showMessageDialog(mainForm, msg, "Error",
            JOptionPane.ERROR_MESSAGE);
  }
}
