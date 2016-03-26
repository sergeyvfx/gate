package core;

import java.io.File;
import java.io.IOException;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.*;
import java.nio.file.Files;
import java.util.Map;

public class RequestSender {    
    private String boundary = "";
    private String CRLF = "\r\n";
    
    private HttpURLConnection getConnection(String urlLocation) throws IOException {
       boundary = Long.toHexString(System.currentTimeMillis());
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
   public String sendPostRequest(String urlLocation, java.util.List parameters, File file) throws IOException {
       StringBuilder result = new StringBuilder();
       HttpURLConnection connection = getConnection(urlLocation);
       connection.setRequestMethod("POST");
       connection.setRequestProperty("Referer", urlLocation);
       connection.setRequestProperty("Cookie", "your cookies may be here");
       /*String data = "";
       if (parameters != null) {
           for (int i = 0; i < parameters.size(); i++) {
                String param[] = (String[]) parameters.get(i);
                /*if (i != 0) {
                    data = data + "&";
                }
                data = data + param[0] + "=" + URLEncoder.encode(param[1], "UTF-8");
               
                data += "--" + boundary + CRLF;
                data += "Content-Disposition: form-data; name=\"" + param[0] + "\"" + CRLF;
                data += "Content-Type: text/plain; charset=utf-8" + CRLF;
                data += CRLF + param[1]+ CRLF;
           }
       }*/
       
       /*if (parameters != null && data.length() != 0) {
           connection.setRequestProperty("Content-Length", Integer.toString(data.length()));
       }*/
       connection.connect();
       if (parameters != null/* && data.length() != 0*/) {
            OutputStream output = connection.getOutputStream();
            PrintWriter writer = new PrintWriter(output);
            
            for (int i = 0; i < parameters.size(); i++) {
                String param[] = (String[]) parameters.get(i);
               
                writer.append("--" + boundary).append(CRLF);
                writer.append("Content-Disposition: form-data; name=\"" + param[0] + "\"").append(CRLF);
                writer.append("Content-Type: text/plain; charset=utf-8").append(CRLF);
                writer.append(CRLF).append(param[1]).append(CRLF).flush();
            }
            
            if (file != null) {
                writer.append("--" + boundary).append(CRLF);
                writer.append("Content-Disposition: form-data; name=\"answer_file\"; filename=\"" + file.getName() + "\"").append(CRLF);
                writer.append("Content-Type: " + URLConnection.guessContentTypeFromName(file.getName())).append(CRLF);
                writer.append("Content-Transfer-Encoding: binary").append(CRLF);
                writer.append(CRLF).flush();
                Files.copy(file.toPath(), output);
                output.flush(); // Important before continuing with writer!
                writer.append(CRLF).flush(); // CRLF is important! It indicates end of boundary.
            }
            
            // End of multipart/form-data.
            writer.append("--" + boundary + "--").append(CRLF).flush();
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
       
       //connection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
       connection.setRequestProperty("Content-Type", "multipart/form-data; boundary=" + boundary);
       
       connection.setRequestProperty("Pragma", "no-cache");
       connection.setInstanceFollowRedirects(false);
   }
}
