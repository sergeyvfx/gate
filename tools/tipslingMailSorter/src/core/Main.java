/**
 * Tipsling Mail Sorter - small tool for sort mail
 *
 * Copyright (c) 2011 Alexander O. Anisimov <alenyashka@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */

package core;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Properties;
import java.util.SimpleTimeZone;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import javax.mail.Flags;
import javax.mail.Folder;
import javax.mail.Message;
import javax.mail.Multipart;
import javax.mail.Part;
import javax.mail.Session;
import javax.mail.Store;
import javax.mail.internet.MailDateFormat;
import javax.mail.internet.MimeUtility;

public class Main {

  private Properties config;

  public Main() {
    config = new Properties();
    String fileName = "tipslingMailSorter.cfg";
    try {
      InputStream is = new FileInputStream(fileName);
      config.load(is);
    } catch (IOException ex) {
      System.err.println("Не могу прочитать файл настроек!");
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
    private Properties props = new Properties();
    private Connection conn = null;
    String mysqlUserName = config.getProperty("mysql.username");
    String mysqlPassword = config.getProperty("mysql.password");
    String mysqlURL = config.getProperty("mysql.url");

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

    public void main() {
      try {
        Session session = Session.getDefaultInstance(props, null);
        Store store = session.getStore(provider);
        store.connect(host, login, password);

        Folder inbox = store.getFolder("INBOX");
        if (inbox == null) {
          log("Почты нет", false);
          return;
        }
        inbox.open(Folder.READ_WRITE);

        Message[] messages = inbox.getMessages();

        if (messages.length > 0) {
          try {
            Class.forName("com.mysql.jdbc.Driver").newInstance();
            conn = DriverManager.getConnection(mysqlURL, mysqlUserName, mysqlPassword);
            log("Соединение с базой данных установлено", false);
          } catch (Exception ex) {
            log("Не удается установить соединение с БД", true);
            log(ex.getMessage(), true);
            return;
          }
        }
        for (int i = 0; i < messages.length; i++) {
          String subject = messages[i].getSubject();
          subject = subject.replaceAll("Fwd:", "");
          subject = subject.replaceAll("\\ ", "");
          subject = subject.replaceAll("\"", "");
          Matcher m = Pattern.compile("^[0-9]{0,}\\.[0-9]{0,}\\-[0-9]{0,}$").matcher(subject);
          log("[" + subject + "] - Получено сообщение", false);
          if (!m.matches()) {
            log("[" + subject + "] - Тема сообщения не соответствует формату", false);
            messages[i].setFlag(Flags.Flag.DELETED, true);
          } else {
            Integer grade = new Integer(subject.substring(0, subject.indexOf(".")));
            Integer number = new Integer(subject.substring(subject.indexOf(".") + 1, subject.indexOf("-")));
            Integer task = new Integer(subject.substring(subject.indexOf("-") + 1, subject.length()));
            String recieve = messages[i].getHeader("Received")[0];
            recieve = recieve.substring(recieve.lastIndexOf(";") + 1, recieve.length());
            Date recieveDate = null;
            DateFormat f = new MailDateFormat();
            f.setTimeZone(new SimpleTimeZone(2, "ID"));
            recieveDate = f.parse(recieve);
            log("[" + subject + "] - " + recieveDate.toString(), false);
            messages[i].setFlag(Flags.Flag.DELETED, true);
            Object content = messages[i].getContent();
            if (content instanceof Multipart) {
              Multipart mp = (Multipart) content;
              int size = 0;
              for (int j = 0; j < mp.getCount(); j++) {
                Part p = mp.getBodyPart(j);
                String disposition = p.getDisposition();
                if ((disposition != null) && (disposition.equals(Part.ATTACHMENT) || disposition.equals(Part.INLINE))) {
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
                    log("[" + subject + "] - Такой файл уже есть на диске", false);
                  } else {
                    File saveFile = new File(localPath, fileName);
                    size = saveFile(saveFile, p);
                    saveFile.setReadable(true, false);
                    saveFile.setWritable(true, false);
                    saveFile.setExecutable(false, false);
                    log("[" + subject + "] - Размер вложения: " + size + " bytes", false);
                  }
                }
              }
              addInfo(grade, number, task, recieveDate, size, subject);
            }
          }
        }
        inbox.close(true);
        store.close();
        if (conn != null) {
          conn.close();
          conn = null;
        }
      } catch (Exception ex) {
        log(ex.getMessage(), true);
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

    private void addInfo(int grade, int number, int task, Date date, int size, String subject) {
      DateFormat df = new SimpleDateFormat("HH:mm:ss");
      try {
        Statement s = conn.createStatement();
        ResultSet rs = s.executeQuery("SELECT `team`.`id` FROM `team` WHERE `team`.`grade`=" + grade + " AND `team`.`number`=" + number);
        int teamId = -1;
        if (rs.next()) {
          teamId = rs.getInt("id");
        }
        rs.close();
        if (teamId != -1) {
          s.executeUpdate("INSERT INTO contest_status (contest_id, task, team_id, time, size) VALUES "
                  + "(" + 1 + ", " + task + ", " + teamId + ", " + "'" + df.format(date) + "', " + size + ")");
        } else {
          log("Ошибка при получении ID команды", false);
        }
        s.close();
        log("[" + subject + "] - Информация внесена в БД", false);
      } catch (SQLException ex) {
        log(ex.getMessage(), true);
      }
    }
  }

  public static void main(String[] args) {
    Main mainForm = new Main();
    mainForm.start();
  }
}
