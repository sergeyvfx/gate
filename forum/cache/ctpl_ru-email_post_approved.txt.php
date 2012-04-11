<?php if (!defined('IN_PHPBB')) exit; ?>Subject: Сообщение одобрено — «<?php echo (isset($this->_rootref['POST_SUBJECT'])) ? $this->_rootref['POST_SUBJECT'] : ''; ?>»

Здравствуйте, <?php echo (isset($this->_rootref['USERNAME'])) ? $this->_rootref['USERNAME'] : ''; ?>!

Вы получили это уведомление потому, что Ваше сообщение «<?php echo (isset($this->_rootref['POST_SUBJECT'])) ? $this->_rootref['POST_SUBJECT'] : ''; ?>» на форуме «<?php echo (isset($this->_rootref['SITENAME'])) ? $this->_rootref['SITENAME'] : ''; ?>» было одобрено модератором или администратором.

Если Вы хотите просмотреть свое сообщение, перейдите по следующей ссылке:
<?php echo (isset($this->_rootref['U_VIEW_POST'])) ? $this->_rootref['U_VIEW_POST'] : ''; ?>


Если Вы хотите просмотреть тему, перейдите по следующей ссылке:
<?php echo (isset($this->_rootref['U_VIEW_TOPIC'])) ? $this->_rootref['U_VIEW_TOPIC'] : ''; ?>



<?php echo (isset($this->_rootref['EMAIL_SIG'])) ? $this->_rootref['EMAIL_SIG'] : ''; ?>