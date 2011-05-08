package tablechecker.core.libreoffice;

import com.sun.star.comp.helper.BootstrapException;
import com.sun.star.lang.XMultiComponentFactory;
import com.sun.star.uno.XComponentContext;

public class LibreOffice {

  private static LibreOffice instance;
  private boolean connected = false;
  private XComponentContext xContext = null;
  private XMultiComponentFactory xMCF = null;

  private LibreOffice() {
  }

  public static LibreOffice getInstance() {
    if (instance == null) {
      instance = new LibreOffice();
    }
    return instance;
  }
  
  public void connect() {
    try {
      xContext = com.sun.star.comp.helper.Bootstrap.bootstrap();
      xMCF = xContext.getServiceManager();
      if (xMCF == null) {
        connected = false;
        //TODO Генерировать исключение о неудачном подключении
      } else {
        connected = true;
      }
    } catch (BootstrapException ex) {
      //TODO Прикрутить логирование
    }
  }

  public boolean isConnected() {
    return connected;
  }
}
