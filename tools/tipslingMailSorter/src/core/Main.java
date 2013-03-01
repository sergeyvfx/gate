/**
 * Tipsling Mail Sorter - small tool for sort mail
 *
 * Copyright (c) 2011 Alexander O. Anisimov <alenyashka@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */

package core;

import java.io.*;
import java.sql.Connection;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.LinkedList;
import java.util.Properties;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import javax.mail.*;
import javax.mail.internet.MailDateFormat;
import javax.mail.internet.MimeUtility;

enum MessageType {
    error, warning, empty
}

public class Main {

  private Properties config;

  public Main() {
    config = new Properties();
    String fileName = "tipslingMailSorter.cfg";
    try {
      InputStream is = new FileInputStream(fileName);
      System.err.println(fileName);
      config.load(is);
    } catch (IOException ex) {
      System.err.println("Can't read config file!");
      System.err.println(ex.getMessage());
      System.exit(1);
    }
  }

  public void start() {
    Thread receiver = new Thread(new Receiver());
    receiver.start();
  }

  private void log(String msg, boolean err) {
    Date d = new Date();
    msg = d.toString() + " : " + msg;
    if (err) {
      System.err.println(msg);
    } else {
      System.out.println(msg);
    }
  }
  
  private class Receiver implements Runnable {

    private int TIMEOUT = Integer.parseInt(config.getProperty("mail.timeout", "30000"));
    private String host = config.getProperty("mail.host");
    private String login = config.getProperty("mail.login");
    private String password = config.getProperty("mail.password");
    private String provider = "pop3";
    private String path = new File(config.getProperty("store.path")).getAbsolutePath();
    private String page = config.getProperty("tipsling.page");
    private String code = config.getProperty("tipsling.MonitorCode");
    private String contest_id = config.getProperty("tipsling.contest_id");
    private String log_file = config.getProperty("log.path");
    private Properties props = new Properties();
    private Connection conn = null;
    
    private SimpleDateFormat dateFormat = new SimpleDateFormat("dd.MM.yyyy HH:mm:ss");

    @Override
    public void run() {
      while (true) {
        main();
        try {
          Thread.sleep(TIMEOUT);
        } catch (InterruptedException ex) {
        }
      }
    }
    
    private void log_to_file(String msg, MessageType type) {
      Date d = new Date();
      String msg_type;
      if (type == MessageType.error)
          msg_type = "ОШИБКА! ";
      else if (type == MessageType.warning)
          msg_type = "Внимание! ";
      else
          msg_type = "Действие выполнено. ";
      msg = dateFormat.format(d) + " : " + msg_type + msg + "\n";
      try
      {
        FileWriter wrt = new FileWriter(log_file, true);
        wrt.append(msg);
        wrt.flush();
      }
      catch (Exception ex)
      {
          log(ex.getMessage(), true);
      }
    }

    public void main() {
      try {
        Session session = Session.getDefaultInstance(props, null);
        Store store = session.getStore(provider);
        store.connect(host, login, password);

        Folder inbox = store.getFolder("INBOX");
        if (inbox == null) {
          log("There are no mail", false);
          log_to_file("Нет почты", MessageType.empty);
          return;
        }
        inbox.open(Folder.READ_WRITE);

        Message[] messages = inbox.getMessages();

        for (int i = 0; i < messages.length; i++) {
          //FIXME Realy?! Is it possible to get message from server with deleted flag?
          if (messages[i].getFlags().contains(Flags.Flag.DELETED)) {
              continue;
          }
          String subject = messages[i].getSubject();
          if (subject==null) {
              subject = "";
          }
          subject = subject.replaceAll("Fwd:", "");
          subject = subject.replaceAll("\\ ", "");
          subject = subject.replaceAll("\"", "");
          Matcher m = Pattern.compile("^[0-9]{0,}\\.[0-9]{0,}\\-[0-9]{0,}$").matcher(subject);
          {
              log("[" + subject + "] - message is recieved", false);
              log_to_file("["+subject+"] - Получено сообщение", MessageType.empty);
          }
          if (!m.matches()) {
            log("[" + subject + "] - subject of message don't match contest format", false);
            log_to_file("["+subject+"] - Тема письма не совпадает с форматом конкурса", MessageType.warning);
            messages[i].setFlag(Flags.Flag.DELETED, true);
          } else {
            Integer grade = new Integer(subject.substring(0, subject.indexOf(".")));
            Integer number = new Integer(subject.substring(subject.indexOf(".") + 1, subject.indexOf("-")));
            Integer task = new Integer(subject.substring(subject.indexOf("-") + 1, subject.length()));
            String recieve = messages[i].getHeader("Received")[0];
            recieve = recieve.substring(recieve.lastIndexOf(";") + 1, recieve.length());
            DateFormat f = new MailDateFormat();
            Date recieveDate = f.parse(recieve);
            recieveDate.setTime(recieveDate.getTime() + 3600000);
            log("[" + subject + "] - " + recieveDate.toString(), false);
            log_to_file("["+subject+"] - Дата получения письма: " + dateFormat.format(recieveDate), MessageType.empty);
            messages[i].setFlag(Flags.Flag.DELETED, true);
            Object content = messages[i].getContent();
            if (content instanceof Multipart) {
              Multipart mp = (Multipart) content;
              int size = 0;
              for (int j = 0; j < mp.getCount(); j++) {
                Part p = mp.getBodyPart(j);
                String disposition = p.getDisposition();
                if ((disposition != null) && (disposition.equals(Part.ATTACHMENT))) {
                  String fileName = MimeUtility.decodeText(p.getFileName());
                  fileName = subject + fileName.substring(fileName.lastIndexOf("."), fileName.length());
                  String localPath = path + File.separator + task.toString();
                  File saveDir = new File(localPath);
                  if (!saveDir.exists()) {
                    saveDir.mkdir();
                    saveDir.setExecutable(true, false);
                    saveDir.setWritable(true, false);
                    saveDir.setReadable(true, false);
                  }
                  if (new File(localPath, fileName).exists()) {
                    log("[" + subject + "] - This file is already on disk", false);
                    log_to_file("["+subject+"] - Файл уже существует на диске", MessageType.warning);
                  } else {
                    File saveFile = new File(localPath, fileName);
                    size = saveFile(saveFile, p);
                    saveFile.setReadable(true, false);
                    saveFile.setWritable(true, false);
                    saveFile.setExecutable(false, false);
                    log("[" + subject + "] - The size of attachment: " + size + " bytes", false);
                    log_to_file("["+subject+"] - Размер вложения: " + size + " байт", MessageType.empty);
                  }
                }
              }
              SendPostQuery(grade, number, task, recieveDate, size, subject, code, contest_id);
            }
          }
        }
        if (inbox.isOpen()) {
            inbox.close(true);
        }
        if (store.isConnected()) {
            store.close();
        }
        if (conn != null) {
          conn.close();
          conn = null;
        }
      } catch (Exception ex) {
        log(ex.getMessage(), true);
        log_to_file(ex.getMessage(), MessageType.error);
      }
    }

    private int saveFile(File saveFile, Part part) throws Exception {

      BufferedOutputStream bos = new BufferedOutputStream(
              new FileOutputStream(saveFile));

      byte[] buff = new byte[2048];
      InputStream is = part.getInputStream();
      int ret = 0, count = 0;
      while ((ret = is.read(buff)) > 0) {
        bos.write(buff, 0, ret);
        count += ret;
      }
      bos.close();
      is.close();
      return count;
    }
    
    private void SendPostQuery(int grade, int number, int task, Date date, int size, String subject, String code, String contest_id)
    {
       DateFormat df = new SimpleDateFormat("HH:mm:ss");
        
       //создаём лист параметров для запроса
       java.util.List<String[]> params = new LinkedList<String[]>();
       params.add(new String[]{"id", code});
       params.add(new String[]{"grade", String.valueOf(grade)});
       params.add(new String[]{"number", String.valueOf(number)});
       params.add(new String[]{"task", String.valueOf(task)});
       params.add(new String[]{"date", df.format(date)});
       params.add(new String[]{"size", String.valueOf(size)});
       params.add(new String[]{"contest_id", contest_id});
       
       try {
           //отправляем запрос
           RequestSender sender = new RequestSender();
           String response = sender.sendPostRequest(page, params);
           if (response == null || "".equals(response.trim())) {
               log("[" + subject + "] - The information entered into the database", false);
               log_to_file("[" + subject + "] - Информация внесена в БД", MessageType.empty);
           } else {
               log(response, true);
               log_to_file(response, MessageType.error);
           }
       } catch (IOException ex) {
           log(ex.getMessage(), true);
           log_to_file(ex.getMessage(), MessageType.error);
       }
    }    
  }

  public static void main(String[] args) {
    Main mainForm = new Main();
    mainForm.start();
  }
}
