package core;

import java.io.IOException;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.*;
import java.util.Map;

public class RequestSender {    
    
    private HttpURLConnection getConnection(String urlLocation) throws IOException {
       URL url = new URL(urlLocation);
       HttpURLConnection connection = null;
       connection = (HttpURLConnection) url.openConnection();
       connection.setDoInput(true);
       connection.setDoOutput(true);
       connection.setConnectTimeout(Integer.MAX_VALUE);
       populateConnectionWithStandartProperties(connection);
       return connection;
   }
    
       /**
    * Возвращает все cookie, которые сервер присылает нам с ответом
    */
   private String getResponseCookies(HttpURLConnection connection) {
       Map responseHeaders = connection.getHeaderFields();
       java.util.List responseCookies = (java.util.List) responseHeaders.get("Set-Cookie");
       String allCookies = "";
       if (responseCookies != null) {
           for (int i = 0; i < responseCookies.size(); i++) {
               allCookies = allCookies + responseCookies.get(i) + " ";
           }
       }
       return allCookies;
   }

   /**
    * Посылает POST запрос и возвращает response code, cookies и исходный код страницы.
    */
   public String sendPostRequest(String urlLocation, java.util.List parameters) throws IOException {
       StringBuilder result = new StringBuilder();
       HttpURLConnection connection = getConnection(urlLocation);
       connection.setRequestMethod("POST");
       connection.setRequestProperty("Referer", urlLocation);
       connection.setRequestProperty("Cookie", "your cookies may be here");
       String data = "";
       if (parameters != null) {
           for (int i = 0; i < parameters.size(); i++) {
               String param[] = (String[]) parameters.get(i);
               if (i != 0) {
                   data = data + "&";
               }
               data = data + param[0] + "=" + URLEncoder.encode(param[1], "UTF-8");
           }

       }
       if (parameters != null && data.length() != 0) {
           connection.setRequestProperty("Content-Length", Integer.toString(data.length()));
       }
       connection.connect();
       if (parameters != null && data.length() != 0) {
           PrintWriter out = new PrintWriter(connection.getOutputStream());
           out.write(data);
           out.flush();
       }

       //TODO: Надо бы наверное лучше обрабатывать код ответа
       BufferedReader rd = new BufferedReader(new InputStreamReader(connection.getInputStream()));
       String line;
       while ((line = rd.readLine()) != null) {
           result.append(line).append("\n");
       }
       connection.disconnect();
       return result.toString();
   }
    
    /**
    * Указываем в запросе обычные для браузеров параметры
    */
   private void populateConnectionWithStandartProperties(HttpURLConnection connection) {
       connection.setRequestProperty("Accept", "text/html");
       connection.setRequestProperty("Accept-Language", "en-US");
       connection.setRequestProperty("User-Agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.1.12) Gecko/20080201 Firefox");
       connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
       connection.setRequestProperty("Pragma", "no-cache");
       connection.setInstanceFollowRedirects(false);
   }
}
