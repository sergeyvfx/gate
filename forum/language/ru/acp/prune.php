<?php
/**
*
* acp_prune [Russian]
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

// User pruning
$lang = array_merge($lang, array(
	'ACP_PRUNE_USERS_EXPLAIN'		=> 'Здесь Вы можете удалять или отключать пользователей форума. Аккаунты можно отфильтровать различными способами — по количеству сообщений, по времени последнего посещения, т. д. Критерии могут быть объединены с целью сокращения аккаунтов, к которым будет применена очистка. Например, Вы можете удалить пользователей, которые не посещали конференцию после 01.01.2002, и имеющих менее 10 сообщений. Также Вы можете полностью опустить выбор критериев, вручную введя необходимых пользователей (каждый пользователь должен быть введен с новой строки) в текстовое поле. Будьте осторожны с этим инструментом — восстановление удалённых пользователей невозможно!',

	'DEACTIVATE_DELETE'				=> 'Деактивировать или удалит',
	'DEACTIVATE_DELETE_EXPLAIN'		=> 'Выберите операцию над выбранными пользователями. Учтите, что отменить её будет невозможно!',
	'DELETE_USERS'					=> 'Удалить',
	'DELETE_USER_POSTS'				=> 'Удалить также их сообщения',
	'DELETE_USER_POSTS_EXPLAIN'		=> 'Удаляет пользователей вместе с их сообщениями. Не работает, если выбрана деактивация пользователей.',

	'JOINED_EXPLAIN'				=> 'Введите дату в формате <kbd>ГГГГ-ММ-ДД</kbd>.',

	'LAST_ACTIVE_EXPLAIN'			=> 'Введите дату в формате <kbd>ГГГГ-ММ-ДД</kbd>. Введите <kbd>0000-00-00</kbd> для очистки пользователей, которые никогда не входили на форум, <em>до</em> и <em>после</em> игнорирования условий.',

	'PRUNE_USERS_LIST'					=> 'Список очистки',
	'PRUNE_USERS_LIST_DELETE'			=> 'По заданным критериям очистки будут удалены следующие аккаунты пользователей.',
	'PRUNE_USERS_LIST_DEACTIVATE'		=> 'По заданным критериям очистки будут деактивированы следующие аккаунты пользователей.',

	'SELECT_USERS_EXPLAIN'			=> 'Введите здесь конкретные имена пользователей, если не хотите использовать критерии поиска выше. Примечание: Вы не можете указать основателей.',

	'USER_DEACTIVATE_SUCCESS'		=> 'Отобранные пользователи успешно деактивированы.',
	'USER_DELETE_SUCCESS'			=> 'Отобранные пользователи успешно удалены.',
	'USER_PRUNE_FAILURE'			=> 'Нет пользователей, подходящих под указанные критерии.',

	'WRONG_ACTIVE_JOINED_DATE'		=> 'Недопустимый формат даты. Дата должна быть в формате <kbd>ГГГГ-ММ-ДД</kbd>.',
));

// Forum Pruning
$lang = array_merge($lang, array(
	'ACP_PRUNE_FORUMS_EXPLAIN'		=> 'Будут удалены все темы, в которые не писали и которые не просматривали указанное количество дней. Если Вы не введете число дней, то будут удалены все темы. По умолчанию темы, в которых продолжаются голосования, а также прилепленные темы и объявления не будут удалены.',

	'FORUM_PRUNE'			=> 'Чистка форумов',

	'NO_PRUNE'				=> 'Чистка форумов не произведена.',

	'SELECTED_FORUM'		=> 'Выбран форум',
	'SELECTED_FORUMS'		=> 'Выбраны форумы',

	'POSTS_PRUNED'						=> 'Сообщений удалено',
	'PRUNE_ANNOUNCEMENTS'				=> 'Удалять объявления',
	'PRUNE_FINISHED_POLLS'				=> 'Удалять оконченные голосования',
	'PRUNE_FINISHED_POLLS_EXPLAIN'		=> 'Удалять темы, в которых голосования завершены.',
	'PRUNE_FORUM_CONFIRM'				=> 'Восстановить удаленные темы и сообщения невозможно. Вы действительно хотите очистить выбранные форумы с указанными параметрами?',
	'PRUNE_NOT_POSTED'					=> 'Дней с последнего сообщения',
	'PRUNE_NOT_VIEWED'					=> 'Дней с последнего просмотра',
	'PRUNE_OLD_POLLS'					=> 'Удалять старые голосования',
	'PRUNE_OLD_POLLS_EXPLAIN'			=> 'Удалять темы, в голосованиях которых не было голосов за указанное выше количество дней.',
	'PRUNE_STICKY'						=> 'Удалять прилепленные темы',
	'PRUNE_SUCCESS'						=> 'Форумы успешно вычищены',

	'TOPICS_PRUNED'			=> 'Тем удалено',
));

?>