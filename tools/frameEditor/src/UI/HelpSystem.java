/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package UI;;

import java.awt.event.ActionEvent;
import java.io.File;
import java.net.URL;
import java.util.HashMap;
import javax.help.*;
import javax.help.CSH.DisplayHelpFromSource;

/**
 *
 * @author nazgul
 */
public class HelpSystem {
  private static HelpSystem instance = null;
  private HashMap<String, HelpSet> hsMap;
  private HashMap<Integer, HelpBroker> hbMap;

  private HelpSystem ()
  {
    hsMap = new HashMap<String, HelpSet> ();
    hbMap = new HashMap<Integer, HelpBroker> ();
  }

  static public HelpSystem getInstance ()
  {
    if (instance == null)
      {
        instance = new HelpSystem ();
      }

    return instance;
  }

  private String getHelpSetFile ()
  {
    String prefix = "";

    // XXX: fix for internacionalization
    prefix = "ru";

    return prefix + File.separator + "helpset.hs";
  }

  private HelpSet getHelpSet ()
  {
    ClassLoader cl = HelpSystem.class.getClassLoader ();
    String fileName = getHelpSetFile ();
    HelpSet dummy;

    dummy = hsMap.get (fileName);
    if (dummy != null)
      {
        return dummy;
      }

    try
      {
        URL hsURL = HelpSet.findHelpSet (cl, "Help/" + fileName);
        dummy = new HelpSet (null, hsURL);
        hsMap.put (fileName, dummy);
        return dummy;
      }
    catch (Exception ee)
      {
        System.out.println ("HelpSet " + ee.getMessage ());
        System.out.println ("HelpSet " + fileName +" not found");
        return null;
      }
  }

  private HelpBroker getHelpBroker ()
  {
    HelpSet hs = getHelpSet ();

    if (hs == null)
      {
        return null;
      }

    HelpBroker hb;
    Integer hash = new Integer (0); // XXX: fix for internacionalization

    hb = hbMap.get (hash);
    if (hb != null)
      {
        return hb;
      }

    hb = hs.createHelpBroker ();

    hbMap.put (hash, hb);

    return hb;
  }

  public void showContents (Object source)
  {
    showContents (source, "Intro");
  }

  public void showContents (Object source, String id)
  {
    HelpBroker hb = getHelpBroker ();

    if (hb == null)
      {
        return;
      }

    DisplayHelpFromSource dummy = new CSH.DisplayHelpFromSource (hb);

    if (dummy != null)
      {
        if (!hb.isDisplayed ())
          {
            dummy.actionPerformed (new ActionEvent (source, 0, ""));
          }

        hb.setDisplayed(true);
        if (id != null)
          {
            try
              {
                hb.setCurrentID (id);
              }
            catch (Exception e)
              {

              }
          }
      }
  }
}
