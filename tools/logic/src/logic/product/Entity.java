package logic.product;

import java.util.HashMap;
import javax.swing.ImageIcon;

public class Entity
{
  public static final String VEHICLE = "Транспортное средство";
  public static final String SIGN = "Знак";
  public static final String TRAFFIC_LIGHT = "Светофор";
  // Тип сущности
  private String type = "";
  // Ассоциированное изображение
  ImageIcon image = null;
  // Свойства
  HashMap<String, String> properties = null;
  // Координаты
  private int x;
  private int y;

  public Entity(String type, ImageIcon image, HashMap<String, String> prop, int x, int y)
  {
    this.type = type;
    this.image = image;
    this.properties = prop;
    this.x = x;
    this.y = y;
  }

  public String getType()
  {
    return type;
  }

  public void setImage(ImageIcon image)
  {
    this.image = image;
  }

  public HashMap<String, String> toHashMap(int c) {
    HashMap<String, String> res = new HashMap<String, String>();
    String s = getType() + " " + Integer.toString(c) + ".";
    for (String key : properties.keySet()) {
      res.put(s + key, properties.get(key));
    }
    res.put(s + "X", Integer.toString(x));
    res.put(s + "Y", Integer.toString(y));
    return res;
  }

  public HashMap<String, String> toHashMap() {
    HashMap<String, String> res = new HashMap<String, String>();
    String s = "Локатив." + getType() + ".";
    for (String key : properties.keySet()) {
      res.put(s + key, properties.get(key));
    }
    return res;
  }

  public ImageIcon getImage()
  {
    return image;
  }

  public HashMap<String, String> getProperties()
  {
    return properties;
  }

  public void setX(int x)
  {
    this.x = x;
  }

  public void setY(int y)
  {
    this.y = y;
  }

  public int getX()
  {
    return x;
  }

  public int getY()
  {
    return y;
  }
}
