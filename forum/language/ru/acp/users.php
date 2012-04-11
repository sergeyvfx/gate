<?php
/**
*
* acp_users [Russian]
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

$lang = array_merge($lang, array(
	'ADMIN_SIG_PREVIEW'			=> 'Просмотр подписи',
	'AT_LEAST_ONE_FOUNDER'		=> 'Вы не можете понизить статус этого основателя до обычного пользователя. На форуме должен быть по крайней мере один основатель. Если Вы хотите изменить статус основателя для этого пользователя, сначала сделайте основателем другого пользователя.',

	'BAN_ALREADY_ENTERED'		=> 'Ранее запрет уже был осуществлен. Черный список не обновлен.',
	'BAN_SUCCESSFUL'			=> 'Пользователь успешно добавлен в черный список.',
	'CANNOT_BAN_ANONYMOUS'		=> 'Вы не можете закрыть доступ для анонимного пользователя. Права доступа для гостей могут быть заданы на соответствующей вкладке центра администрирования.',

	'CANNOT_BAN_FOUNDER'				=> 'Вы не можете закрыть доступ основателям.',
	'CANNOT_BAN_YOURSELF'				=> 'Вы не можете закрыть доступ самому себе.',
	'CANNOT_DEACTIVATE_BOT'				=> 'Вы не можете деактивировать аккаунты ботов. Пожалуйста, деактивируйте бота.',
	'CANNOT_DEACTIVATE_FOUNDER'			=> 'Вы не можете деактивировать аккаунты основателей.',
	'CANNOT_DEACTIVATE_YOURSELF'		=> 'Вы не можете деактивировать собственный аккаунт.',
	'CANNOT_FORCE_REACT_BOT'			=> 'Вы не можете требовать повторной активации аккаунта бота. Пожалуйста, произведите повторную активацию бота.',
	'CANNOT_FORCE_REACT_FOUNDER'		=> 'Вы не можете требовать повторной активации аккаунта основателя.',
	'CANNOT_FORCE_REACT_YOURSELF'		=> 'Вы не можете требовать повторной активации собственного аккаунта.',
	'CANNOT_REMOVE_ANONYMOUS'			=> 'Вы не можете удалить аккаунт гостя.',
	'CANNOT_REMOVE_YOURSELF'			=> 'Вы не можете удалить собственный аккаунт.',
	'CANNOT_SET_FOUNDER_IGNORED'		=> 'Вы не можете сделать игнорируемых пользователей основателями.',
	'CANNOT_SET_FOUNDER_INACTIVE'		=> 'Вы должны активировать пользователей, чтобы сделать их основателями. Только активированным пользователям можно повысить статус.',
	'CONFIRM_EMAIL_EXPLAIN'				=> 'Это поле необходимо заполнить только если Вы изменили e-mail адрес пользователя.',

	'DELETE_POSTS'				=> 'Удалить сообщения',
	'DELETE_USER'				=> 'Удалить пользователя',
	'DELETE_USER_EXPLAIN'		=> 'Учтите, что удаление пользователя необратимо, он не может быть восстановлен',

	'FORCE_REACTIVATION_SUCCESS'		=> 'Повторная активация успешно произведена.',
	'FOUNDER'							=> 'Основатель',
	'FOUNDER_EXPLAIN'					=> 'Основатели имеют все права администратора и не могут быть ограничены в доступе (заблокированы), удалены или понижены в статусе',

	'GROUP_APPROVE'						=> 'Одобрить пользователя',
	'GROUP_DEFAULT'						=> 'Задать для пользователя группу по умолчанию',
	'GROUP_DELETE'						=> 'Удалить пользователя из группы',
	'GROUP_DEMOTE'						=> 'Снять администратора группы',
	'GROUP_PROMOTE'						=> 'Назначить администратором группы',

	'IP_WHOIS_FOR'				=> 'Об IP %s',

	'LAST_ACTIVE'				=> 'Последнее посещение',

	'MOVE_POSTS_EXPLAIN'		=> 'Выберите форум, в который Вы хотите переместить все сообщения данного пользователя.',

	'NO_SPECIAL_RANK'			=> 'Специального звания не присвоено',
	'NO_WARNINGS'				=> 'Предупреждения отсутствуют.',
	'NOT_MANAGE_FOUNDER'		=> 'Вы попытались управлять аккаунтом пользователя со статусом основателя. Только основатели могут управлять аккаунтами других основателей.',

	'QUICK_TOOLS'				=> 'Быстрые операции',

	'REGISTERED'				=> 'Зарегистрирован',
	'REGISTERED_IP'				=> 'Зарегистрирован с IP-адреса',
	'RETAIN_POSTS'				=> 'Оставить сообщения',

	'SELECT_FORM'				=> 'Выбрать форму',
	'SELECT_USER'				=> 'Выбрать пользователя',

	'USER_ADMIN'						=> 'Управление пользователями',
	'USER_ADMIN_ACTIVATE'				=> 'Активировать аккаунт',
	'USER_ADMIN_ACTIVATED'				=> 'Пользователь успешно активирован.',
	'USER_ADMIN_AVATAR_REMOVED'			=> 'Аватар пользователя успешно удален.',
	'USER_ADMIN_BAN_EMAIL'				=> 'Запретить e-mail адрес',
	'USER_ADMIN_BAN_EMAIL_REASON'		=> 'E-mail адреса, запрещенные через управление пользователями',
	'USER_ADMIN_BAN_IP'					=> 'Запретить IP-адрес',
	'USER_ADMIN_BAN_IP_REASON'			=> 'IP-адреса, запрещенные через систему управления пользователями',
	'USER_ADMIN_BAN_NAME_REASON'		=> 'Имена, запрещенные через систему управления пользователями',
	'USER_ADMIN_BAN_USER'				=> 'Запретить имя пользователя',
	'USER_ADMIN_DEACTIVATE'				=> 'Деактивировать аккаунт',
	'USER_ADMIN_DEACTIVED'				=> 'Пользователь успешно деактивирован.',
	'USER_ADMIN_DEL_ATTACH'				=> 'Удалить все вложения',
	'USER_ADMIN_DEL_AVATAR'				=> 'Удалить аватар',
	'USER_ADMIN_DEL_OUTBOX'				=> 'Папка "Исходящие" пуста',
	'USER_ADMIN_DEL_POSTS'				=> 'Удалить все сообщения',
	'USER_ADMIN_DEL_SIG'				=> 'Удалить подпись',
	'USER_ADMIN_EXPLAIN'				=> 'Здесь Вы можете изменять информацию о пользователях и некоторые специальные настройки.',
	'USER_ADMIN_FORCE'					=> 'Принудительная повторная активация',
	'USER_ADMIN_LEAVE_NR'				=> 'Удалить из группы «Новые пользователи»',
	'USER_ADMIN_MOVE_POSTS'				=> 'Переместить все сообщения',
	'USER_ADMIN_SIG_REMOVED'			=> 'Подпись пользователя успешно удалена.',
	'USER_ATTACHMENTS_REMOVED'			=> 'Все вложения данного пользователя успешно удалены.',
	'USER_AVATAR_NOT_ALLOWED'			=> 'Аватар не отображается, т. к. отображение аватаров запрещено.',
	'USER_AVATAR_UPDATED'				=> 'Информация об аватаре пользователя успешно обновлена.',
	'USER_AVATAR_TYPE_NOT_ALLOWED'		=> 'Текущий аватар не может быть отображен, поскольку его тип запрещён.',
	'USER_CUSTOM_PROFILE_FIELDS'		=> 'Дополнительные поля профиля',
	'USER_DELETED'						=> 'Пользователь успешно удален.',
	'USER_GROUP_ADD'					=> 'Добавить пользователя в группу',
	'USER_GROUP_NORMAL'					=> 'Обычные группы, в которые входит пользователь',
	'USER_GROUP_PENDING'				=> 'Группы, в которых пользователь является кандидатом',
	'USER_GROUP_SPECIAL'				=> 'Специальные группы, в которые входит пользователь',
	'USER_LIFTED_NR'					=> 'Пользователь успешно удалён из группы вновь зарегистрированных пользователей.',
	'USER_NO_ATTACHMENTS'				=> 'Вложения отсутствуют.',
	'USER_OUTBOX_EMPTIED'				=> 'Папка «Исходящие» данного пользователя успешно очищена.',
	'USER_OUTBOX_EMPTY'					=> 'Папка «Исходящие» данного пользователя пуста.',
	'USER_OVERVIEW_UPDATED'				=> 'Информация о пользователе обновлена.',
	'USER_POSTS_DELETED'				=> 'Все сообщения данного пользователя успешно удалены.',
	'USER_POSTS_MOVED'					=> 'Сообщения пользователя успешно перемещены в указанный форум.',
	'USER_PREFS_UPDATED'				=> 'Настройки пользователя обновлены.',
	'USER_PROFILE'						=> 'Профиль пользователя',
	'USER_PROFILE_UPDATED'				=> 'Профиль пользователя обновлен.',
	'USER_RANK'							=> 'Звание пользователя',
	'USER_RANK_UPDATED'					=> 'Звание пользователя обновлено.',
	'USER_SIG_UPDATED'					=> 'Подпись пользователя успешно обновлена.',
	'USER_WARNING_LOG_DELETED'			=> 'Информация недоступна. Возможно, данная запись была удалена из журнала.',
	'USER_TOOLS'						=> 'Основные инструменты',
));

?>