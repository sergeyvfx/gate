package frameEditor;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

public class Events
{
  public interface EventHandler
  {
    public void handler();
  }

  protected HashMap<String, List<EventHandler>> events;

  /**
   * Constructor
   */
  public Events()
  {
    events = new HashMap<String, List<EventHandler>>();
  }

  /**
   * Register event's handler
   *
   * @param event - name of event
   * @param handler - event's handler
   */
  public void registerCallback(String event, EventHandler handler)
  {
    List<EventHandler> list = events.get(event);

    if (list == null)
    {
      list = new ArrayList<EventHandler> ();
      events.put(event, list);
    }

    list.add(handler);
  }

  /**
   * Fire event
   *
   * @param event - name of event to be fired
   */
  public void fireEvent(String event)
  {
    List<EventHandler> list = events.get(event);

    if (list == null)
      return;

    for (int i = 0, n = list.size(); i < n; ++i)
    {
      list.get(i).handler();
    }
  }
}
