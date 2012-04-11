<?php
/**
*
* acp_ban [Russian]
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

// Banning
$lang = array_merge($lang, array(
	'1_HOUR'			=> '1 час',
	'30_MINS'			=> '30 минут',
	'6_HOURS'			=> '6 часов',

	'ACP_BAN_EXPLAIN'		=> 'Здесь Вы можете блокировать пользователям доступ по именам, e-mail  или IP-адресам. Эти методы не позволят пользователю попасть ни в один из разделов форума. При желании Вы можете оставить короткую надпись (до 3000 символов) с описанием причины закрытия доступа, которая будет отображаться в логе администратора. Также можно указать продолжительность закрытия доступа. Если Вы хотите, чтобы блокировка закончилась в определенный день, а не через установленный промежуток времени, то введите дату в формате <kbd>ГГГГ-ММ-ДД</kbd> под списком «Продолжительность закрытия доступа», предварительно выбрав в этом списке опцию <span style="text-decoration: underline;">До даты</span>.',

	'BAN_EXCLUDE'				=> 'Добавить в белый список',
	'BAN_LENGTH'				=> 'Продолжительность отключения',
	'BAN_REASON'				=> 'Причина блокировки доступа',
	'BAN_GIVE_REASON'			=> 'Причина, показываемая пользователю',
	'BAN_UPDATE_SUCCESSFUL'		=> 'Черный список успешно обновлен.',
	'BANNED_UNTIL_DATE'		=> 'до %s', // Например: "до Mon 13.Jul.2009, 14:44"
	'BANNED_UNTIL_DURATION'	=> '%1$s (до %2$s)', // Например: "7 days (до Tue 14.Jul.2009, 14:44)"

	'EMAIL_BAN'						=> 'Запретить один или несколько e-mail адресов ',
	'EMAIL_BAN_EXCLUDE_EXPLAIN'		=> 'Исключить указанные e-mail адреса  из черного списка.',
	'EMAIL_BAN_EXPLAIN'				=> 'Вводите каждый адрес на новой строке. Используйте звездочку (*) в качестве подстановочного знака для блокировки группы однотипных адресов. Например, <samp>*@mail.ru</samp>, <samp>*@*.domain.tld</samp> и т.д.',
	'EMAIL_NO_BANNED'				=> 'Черный список e-mail адресов  пуст',
	'EMAIL_UNBAN'					=> 'Вновь разрешить e-mail адреса  или удалить адреса из белого списка',
	'EMAIL_UNBAN_EXPLAIN'			=> 'За один раз можно разблокировать (или удалить из белого списка) несколько адресов, выбрав их с помощью соответствующей комбинации мыши и клавиатуры Вашего компьютера и браузера. Адреса из белого списка выделены особым цветом.',

	'IP_BAN'						=> 'Блокировать доступ с одного или нескольких IP-адресов',
	'IP_BAN_EXCLUDE_EXPLAIN'		=> 'Исключить указанные IP-адреса из черного списка.',
	'IP_BAN_EXPLAIN'				=> 'Вводите каждый IP-адрес или имя узла на новой строке. Для указания диапазона IP-адресов отделите его начало и конец дефисом (-), или используйте звездочку (*) в качестве подстановочного знака.',
	'IP_HOSTNAME'					=> 'IP-адреса или хосты',
	'IP_NO_BANNED'					=> 'Черный список IP-адресов пуст',
	'IP_UNBAN'						=> 'Вновь разрешить доступ с адресов IP или удалить адреса из белого списка',
	'IP_UNBAN_EXPLAIN'				=> 'За один раз можно разблокировать (или удалить из белого списка) несколько IP-адресов, выбрав их с помощью соответствующей комбинации мыши и клавиатуры Вашего компьютера и браузера. Адреса из белого списка выделены особым цветом.',

	'LENGTH_BAN_INVALID'			=> 'Дата должна быть в формате <kbd>ГГГГ-ММ-ДД</kbd>.',
	'OPTIONS_BANNED'			=> 'Запрещено',
	'OPTIONS_EXCLUDED'			=> 'Исключено',
	'PERMANENT'			=> 'Бессрочно',

	'UNTIL'							=> 'До даты',
	'USER_BAN'						=> 'Заблокировать доступ одному или нескольким пользователям',
	'USER_BAN_EXCLUDE_EXPLAIN'		=> 'Исключить указанных пользователей из черного списка.',
	'USER_BAN_EXPLAIN'				=> 'Вводите каждое имя на новой строке. Используйте ссылку <span style="text-decoration: underline;">Найти пользователя</span> для поиска и автоматического добавления пользователей.',
	'USER_NO_BANNED'				=> 'Черный список пользователей пуст',
	'USER_UNBAN'					=> 'Вновь разрешить доступ пользователям или удалить пользователей из белого списка',
	'USER_UNBAN_EXPLAIN'			=> 'За один раз можно разблокировать (или удалить из белого списка) несколько имен, выбрав их с помощью соответствующей комбинации мыши и клавиатуры вашего компьютера и браузера. Имена из белого списка выделены особым цветом.',

));

?>