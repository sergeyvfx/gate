<?php
/**
*
* acp_email [Russian]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
exit;
}

if (empty($lang) || !is_array($lang))
{
$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

// Email settings
$lang = array_merge($lang, array(
	'ACP_MASS_EMAIL_EXPLAIN'			=> 'Здесь Вы можете отправить e-mail сообщения всем пользователям или членам определенной группы. Для этого e-mail сообщение будет отправлено на административный адрес, с BCC: всем получателям. Если Вы отправляете сообщения большой группе людей, будьте терпеливы и не останавливайте процесс отправки до его полного завершения. Длительное время отправки является нормальным при массовой рассылке, Вы будете уведомлены о завершении операции',
	'ALL_USERS'									=> 'Все пользователи',

	'COMPOSE'										=> 'Создать',

	'EMAIL_SEND_ERROR'						=> 'При отправке сообщения произошла одна или несколько ошибок. Проверьте %Лог ошибок%s для получения более полной информации.',
	'EMAIL_SENT'									=> 'Сообщение было отправлено.',
	'EMAIL_SENT_QUEUE'						=> 'Это сообщение поставлено в очередь для отправки.',

	'LOG_SESSION'								=> 'Вести лог критических ошибок сеанса рассылки',

	'SEND_IMMEDIATELY'						=> 'Отправить немедленно',
	'SEND_TO_GROUP'							=> 'Отправить группе',
	'SEND_TO_USERS'							=> 'Отправить пользователям',
	'SEND_TO_USERS_EXPLAIN'			=> 'Сообщение будет отправлено указанным пользователям вместо выбранной выше группы. Вводите каждое имя пользователя с новой строки.',
	'MAIL_BANNED'								=> 'Почта заблокированных пользователей',
	'MAIL_BANNED_EXPLAIN'					=> 'Когда отправляете письмо группе пользователей, Вы можете выбрать смогут ли заблокированные пользователи также получить ваше письмо.',
	'MAIL_HIGH_PRIORITY'						=> 'Высокий',
	'MAIL_LOW_PRIORITY'						=> 'Низкий',
	'MAIL_NORMAL_PRIORITY'				=> 'Обычный',
	'MAIL_PRIORITY'								=> 'Приоритет рассылки',
	'MASS_MESSAGE'							=> 'Ваше сообщение',
	'MASS_MESSAGE_EXPLAIN'			=> 'Учтите, что Вы можете использовать только обычный текст. Любая разметка будет удалена перед отправкой.',

	'NO_EMAIL_MESSAGE'					=> 'Вы должны ввести текст сообщения.',
	'NO_EMAIL_SUBJECT'						=> 'Вы должны указать тему сообщения.',
));

?>