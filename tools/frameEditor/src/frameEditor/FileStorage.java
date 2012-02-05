/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package frameEditor;

import logic.frames.Frameset;
import org.jgraph.graph.GraphModel;

/**
 *
 * @author nazgul
 */
public class FileStorage
{

  protected Frameset frameset;
  protected GraphModel model;

  public FileStorage(Frameset frameset, GraphModel model)
  {
    this.frameset = frameset;
    this.model = model;
  }

  public Frameset getFrameset()
  {
    return frameset;
  }

  public GraphModel getModel()
  {
    return model;
  }
}
