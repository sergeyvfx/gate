package frameEditor;

import javax.swing.JTree;
import javax.swing.tree.TreeModel;

public class Outliner
{
  protected JTree jTree /* Tree for displaying depgraph */;

  /**
   * Constructor
   *
   * @param jTree - tree fot displaying depgraph
   */
  public Outliner(JTree jTree, Events events)
  {
    this.jTree = jTree;

    events.registerCallback("updateDepgraph",
            new Events.EventHandler() {

              public void handler()
              {
                updateDepgraph();
              }
            });
  }

  public void updateDepgraph()
  {
    /*
     * TODO: Fill jTree here
     */
  }
}
