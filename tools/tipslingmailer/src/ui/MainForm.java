package ui;

import java.awt.Dimension;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.Date;
import java.util.Properties;
import java.util.Scanner;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.util.regex.Pattern;
import javax.activation.DataHandler;
import javax.activation.FileDataSource;
import javax.mail.Authenticator;
import javax.mail.Message;
import javax.mail.MessagingException;
import javax.mail.Multipart;
import javax.mail.PasswordAuthentication;
import javax.mail.Session;
import javax.mail.Transport;
import javax.mail.internet.InternetAddress;
import javax.mail.internet.MimeBodyPart;
import javax.mail.internet.MimeMessage;
import javax.mail.internet.MimeMultipart;
import javax.mail.internet.MimeUtility;
import javax.swing.JButton;
import javax.swing.JFileChooser;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JScrollPane;
import javax.swing.JTextArea;
import javax.swing.JTextField;
import javax.swing.ProgressMonitor;
import javax.swing.filechooser.FileNameExtensionFilter;

public class MainForm extends JFrame {

  private JTextField jtfLogin;
  private JTextField jtfPassword;
  private JTextField jtfSubject;
  private JTextField jtfPath;
  private JTextField jtfAttachment;
  private JTextArea jtaBody;
  private JButton jbBrowse;
  private JButton jbBrowseAttachment;
  private JButton jbOk;
  private JButton jbCancel;
  // Регулярное выражение для проверки корректности e-mail
  private Pattern p = Pattern.compile("^([a-zA-Z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}$");
  private ProgressMonitor pm = null;
  private ArrayList<String> emails;

  public MainForm() {
    createUI();
  }

  private void createUI() {
    setTitle("Tipsling Mailer");
    setLayout(new GridBagLayout());
    jtfLogin = new JTextField();
    jtfPassword = new JPasswordField();
    jtfPath = new JTextField();
    jtfSubject = new JTextField();
    jtfAttachment = new JTextField();
    jtaBody = new JTextArea();
    jtaBody.setWrapStyleWord(true);
    jtaBody.setLineWrap(true);
    jbBrowse = new JButton("Обзор...");
    jbBrowse.addActionListener(new ActionListener() {

      @Override
      public void actionPerformed(ActionEvent e) {
        browse(e);
      }
    });
    jbBrowseAttachment = new JButton("Обзор...");
    jbBrowseAttachment.addActionListener(new ActionListener() {

      @Override
      public void actionPerformed(ActionEvent e) {
        browseAttachment(e);
      }
    });
    jbOk = new JButton("Отправить");
    jbOk.addActionListener(new ActionListener() {

      @Override
      public void actionPerformed(ActionEvent e) {
        ok(e);
      }
    });
    jbCancel = new JButton("Закрыть");
    jbCancel.addActionListener(new ActionListener() {

      @Override
      public void actionPerformed(ActionEvent e) {
        cancel(e);
      }
    });
    add(new JLabel("Логин:"), new GridBagConstraints(0, 0, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    add(jtfLogin, new GridBagConstraints(1, 0, 2, 1, 1, 0,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Пароль:"), new GridBagConstraints(0, 1, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    add(jtfPassword, new GridBagConstraints(1, 1, 2, 1, 1, 0,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Кому:"), new GridBagConstraints(0, 2, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    add(jtfPath, new GridBagConstraints(1, 2, 1, 1, 1, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(jbBrowse, new GridBagConstraints(2, 2, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Тема: "), new GridBagConstraints(0, 3, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    add(jtfSubject, new GridBagConstraints(1, 3, 2, 1, 1, 0,
            GridBagConstraints.NORTH, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(new JScrollPane(jtaBody), new GridBagConstraints(0, 4, 3, 1, 1, 1,
            GridBagConstraints.NORTHWEST, GridBagConstraints.BOTH, new Insets(5, 5, 5, 5), 0, 0));
    add(new JLabel("Вложение:"), new GridBagConstraints(0, 5, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    add(jtfAttachment, new GridBagConstraints(1, 5, 1, 1, 1, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(jbBrowseAttachment, new GridBagConstraints(2, 5, 1, 1, 0, 0,
            GridBagConstraints.BASELINE_LEADING, GridBagConstraints.NONE, new Insets(5, 5, 5, 5), 0, 0));
    JPanel jp = new JPanel(new GridBagLayout());
    jp.add(jbOk, new GridBagConstraints(0, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    jp.add(jbCancel, new GridBagConstraints(1, 0, 1, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(5, 5, 5, 5), 0, 0));
    add(jp, new GridBagConstraints(0, 6, 3, 1, 1, 0,
            GridBagConstraints.NORTHWEST, GridBagConstraints.HORIZONTAL, new Insets(0, 0, 0, 0), 0, 0));
    setSize(640, 480);
    setMinimumSize(new Dimension(640, 480));
  }

  private String getHostName() {
    String res = "";
    String login = jtfLogin.getText();
    res = "smtp." + login.substring(login.indexOf('@') + 1, login.length());
    return res;
  }

  private void sendMail(String to) throws MessagingException, UnsupportedEncodingException {
    Properties properties = new Properties();
    properties.put("mail.transport.protocol", "smtp");
    properties.put("mail.smtp.port", "2525");
    properties.put("mail.smtp.host", getHostName());
    properties.put("mail.smtp.auth", "true");

    Authenticator auth = new SMTPAuthenticator();
    Session mailSession = Session.getInstance(properties, auth);
    Transport transport = mailSession.getTransport("smtp");

    MimeMessage message = new MimeMessage(mailSession);
    message.setFrom(new InternetAddress(jtfLogin.getText()));
    message.addRecipient(Message.RecipientType.TO, new InternetAddress(to));
    message.setSubject(jtfSubject.getText());
    message.setSentDate(new Date());

    // Set e-mail text part
    MimeBodyPart textPart = new MimeBodyPart();
    textPart.setText(jtaBody.getText(), "utf-8");
    textPart.setHeader("Content-Type", "text/plain; charset=\"utf-8\"");
    textPart.setHeader("Content-Transfer-Encoding", "quoted-printable");

    MimeBodyPart attachmentPart = null;
    // Set e-mail attachment part
    String filePath = jtfAttachment.getText();
    if (new File(filePath).exists()) {
      attachmentPart = new MimeBodyPart();
      FileDataSource fileDataSource = new FileDataSource(filePath) {

        @Override
        public String getContentType() {
          return "application/x-rar-compressed";
        }
      };
      attachmentPart.setDataHandler(new DataHandler(fileDataSource));
      String fileName = fileDataSource.getFile().getName();
      attachmentPart.setFileName(MimeUtility.encodeText(fileName));
    }

    Multipart multipart = new MimeMultipart();
    multipart.addBodyPart(textPart);
    if (attachmentPart != null) {
      multipart.addBodyPart(attachmentPart);
    }

    message.setContent(multipart);

    transport.connect();
    transport.sendMessage(message, message.getRecipients(Message.RecipientType.TO));
    transport.close();
  }

  private boolean check() {
    String login = jtfLogin.getText();
    String pathToFile = jtfPath.getText();
    if (login.length() == 0 || !p.matcher(login).matches()) {
      showError("Необходимо заполнить поле \"Логин\"", "Незаполненное поле");
      jtfLogin.requestFocus();
      jtfLogin.selectAll();
      return false;
    }
    if (!(new File(pathToFile).exists())) {
      showError("Необходимо заполнить поле \"Кому\"", "Незаполненное поле");
      jtfPath.requestFocus();
      jtfPath.selectAll();
      return false;
    }
    return true;
  }

  private void browse(ActionEvent e) {
    JFileChooser jfc = new JFileChooser();
    jfc.setFileSelectionMode(JFileChooser.FILES_ONLY);
    jfc.setFileFilter(new FileNameExtensionFilter("Текстовый файл", "txt"));
    jfc.setMultiSelectionEnabled(false);
    int res = jfc.showOpenDialog(jfc);
    if (res == JFileChooser.APPROVE_OPTION) {
      jtfPath.setText(jfc.getSelectedFile().getAbsolutePath());
    }
  }

  private void browseAttachment(ActionEvent e) {
    JFileChooser jfc = new JFileChooser();
    jfc.setFileSelectionMode(JFileChooser.FILES_ONLY);
    jfc.setFileFilter(new FileNameExtensionFilter("RAR архив", "rar"));
    jfc.setMultiSelectionEnabled(false);
    int res = jfc.showOpenDialog(jfc);
    if (res == JFileChooser.APPROVE_OPTION) {
      jtfAttachment.setText(jfc.getSelectedFile().getAbsolutePath());
    }
  }

  private ArrayList<String> readFile() {
    ArrayList<String> res = new ArrayList<String>();
    File f = new File(jtfPath.getText());
    try {
      Scanner scanner = new Scanner(f);
      while (scanner.hasNextLine()) {
        String line = scanner.nextLine();
        if (p.matcher(line).matches()) {
          res.add(line);
        } else {
          System.err.println("E-mail [" + line + "] не корректный!");
        }
      }
    } catch (FileNotFoundException ex) {
      showError("Файл не найден!", "Упс...");
    }
    return res;
  }

  private void ok(ActionEvent e) {
    if (!check()) {
      return;
    }
    int res = JOptionPane.showConfirmDialog(this, "Отправить письмо?",
            "Вы уверены?", JOptionPane.YES_NO_OPTION,
            JOptionPane.QUESTION_MESSAGE);
    if (res == JOptionPane.NO_OPTION) {
      return;
    }
    setElEnable(false);
    emails = readFile();
    pm = new ProgressMonitor(this, "Идет отправка почты...", "Соединение с сервером...", 0, emails.size());
    pm.setMillisToDecideToPopup(0);
    pm.setMillisToPopup(0);
    Thread sender = new Thread(new Sender());
    sender.start();
  }

  private void cancel(ActionEvent e) {
    this.dispose();
  }

  private void setElEnable(boolean b) {
    jtfLogin.setEnabled(b);
    jtfPassword.setEnabled(b);
    jtfPath.setEnabled(b);
    jbBrowse.setEnabled(b);
    jtfSubject.setEditable(b);
    jtaBody.setEnabled(b);
    jbOk.setEnabled(b);
    jbCancel.setEnabled(b);
    jtfAttachment.setEnabled(b);
    jbBrowseAttachment.setEnabled(b);
  }

  private void showError(String msg, String title) {
    JOptionPane.showMessageDialog(this, msg, title, JOptionPane.ERROR_MESSAGE);
  }

  private class SMTPAuthenticator extends Authenticator {

    @Override
    protected PasswordAuthentication getPasswordAuthentication() {
      return new PasswordAuthentication(jtfLogin.getText(), jtfPassword.getText());
    }
  }

  private class Sender implements Runnable {

    private int GROUP_SIZE = 10; // Размер группы сообщений
    private int TIMEOUT_GROUP = 60000; // Одна минута между группами сообщений
    private int TIMEOUT_MESSAGE = 5000; // 5 сек между отдельными сообщениями

    @Override
    public void run() {
      try {
        int i = 0;
        for (String s : emails) {
          i++;
          pm.setNote("Соединение с сервером...");
          sendMail(s);
          System.out.println("+ [" + s + "]");
          pm.setProgress(i);
          pm.setNote(Integer.toString(i) + " из " + Integer.toString(emails.size()));
          Thread.sleep(TIMEOUT_MESSAGE);
          if (i % GROUP_SIZE == 0) {
            Thread.sleep(TIMEOUT_GROUP);
          }
        }
        JOptionPane.showMessageDialog(null, "Все письма успешно отправлены",
                "Уведомление", JOptionPane.INFORMATION_MESSAGE);
      } catch (MessagingException ex) {
        Logger.getLogger(MainForm.class.getName()).log(Level.SEVERE, null, ex);
        showError(ex.getMessage(), "Упс...Ошибочка при отправке");
      } catch (InterruptedException ex) {
        Logger.getLogger(MainForm.class.getName()).log(Level.SEVERE, null, ex);
      } catch (UnsupportedEncodingException ex) {
        Logger.getLogger(MainForm.class.getName()).log(Level.SEVERE, null, ex);
      } finally {
        pm.close();
      }
      setElEnable(true);
    }
  }

  public static void main(String[] args) {
    MainForm mainForm = new MainForm();
    mainForm.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
    mainForm.setVisible(true);
  }
}
