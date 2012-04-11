<?php
/**
*
* acp_database [Russian]
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

// Database Backup/Restore
$lang = array_merge($lang, array(
	'ACP_BACKUP_EXPLAIN'		=> 'Здесь Вы можете создать резервную копию всех данных форума. Вы можете сохранить результирующий архив на сервере в папке <samp>store/</samp> или скачать его. В зависимости от конфигурации сервера может быть доступно сжатие файла резервной копии в нескольких форматах.',
	'ACP_RESTORE_EXPLAIN'		=> 'Будет произведено полное восстановление всех таблиц phpBB из сохраненного файла. Если сервер поддерживает такую возможность, Вы можете использовать сжатые файлы gzip или bzip2, которые будут автоматически разархивированы. <strong>ВНИМАНИЕ</strong> Все существующие данные будут уничтожены. Восстановление может занять длительное время, поэтому не уходите с этой страницы до полного завершения процесса.  Резервные копии, предположительно созданные средствами phpBB, сохранены в папке <samp>store/</samp>. Восстановление из резервных копий, созданных не с использованием встроенной системы, может потерпеть неудачу.',

	'BACKUP_DELETE'			=> 'Файл резервной копии был успешно удален.',
	'BACKUP_INVALID'		=> 'Выбран неверный файл резервной копии.',
	'BACKUP_OPTIONS'		=> 'Настройки резервного копирования',
	'BACKUP_SUCCESS'		=> 'Файл резервной копии успешно создан.',
	'BACKUP_TYPE'			=> 'Вид резервного копирования',

	'DATABASE'				=> 'Управление БД',
	'DATA_ONLY'				=> 'Только данные',
	'DELETE_BACKUP'			=> 'Удалить резервную копию',
	'DELETE_SELECTED_BACKUP'		=> 'Вы действительно хотите удалить выбранную копию?',
	'DESELECT_ALL'			=> 'Снять выделение',
	'DOWNLOAD_BACKUP'		=> 'Скачать резервную копию',

	'FILE_TYPE'				=> 'Тип файла',
	'FILE_WRITE_FAIL'		=> 'Не удалось сохранить файл в папке «store».',
	'FULL_BACKUP'			=> 'Полное',

	'RESTORE_FAILURE'			=> 'Возможно, файл с резервной копией поврежден.',
	'RESTORE_OPTIONS'			=> 'Настройки восстановления',
	'RESTORE_SELECTED_BACKUP'	=> 'Вы действительно хотите восстановить с выбранной резервной копии?',
	'RESTORE_SUCCESS'			=> 'База данных была успешно восстановлена.<br /><br />Форум восстановлен по состоянию на момент создания резервной копии.',

	'SELECT_ALL'				=> 'Выбрать все',
	'SELECT_FILE'				=> 'Выбрать файл',
	'START_BACKUP'				=> 'Начать резервное копирование',
	'START_RESTORE'				=> 'Начать восстановление',
	'STORE_AND_DOWNLOAD'		=> 'Сохранить на сервере и скачать',
	'STORE_LOCAL'				=> 'Сохранить на сервере',
	'STRUCTURE_ONLY'			=> 'Только структура',

	'TABLE_SELECT'			=> 'Выбрать таблицы',
	'TABLE_SELECT_ERROR'=> 'Необходимо выбрать хотя бы одну таблицу.',
));

?>