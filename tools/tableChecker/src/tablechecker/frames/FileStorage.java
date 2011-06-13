package tablechecker.frames;

//import org.jgraph.graph.GraphModel;

public class FileStorage
{

  protected Frameset frameset;
//  protected GraphModel model;

  public FileStorage(Frameset frameset/*, GraphModel model*/)
  {
    this.frameset = frameset;
//    this.model = model;
  }

  public Frameset getFrameset()
  {
    return frameset;
  }

//  public GraphModel getModel()
//  {
//    return model;
//  }
}
