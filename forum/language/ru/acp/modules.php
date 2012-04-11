<?php
/**
*
* acp_modules [Russian]
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
	'ACP_MODULE_MANAGEMENT_EXPLAIN'		=> 'Здесь Вы можете управлять всеми типами модулей. Обратите внимание, что центр администрирования имеет трехуровневую структуру меню (Раздел -> Раздел -> Модуль), в результате чего подразделы имеют двухуровневую структуру меню (Раздел -> Модуль), которая должна быть сохранена. Также учтите, что вы может заблокировать доступ самому себе, если Вы деактивируете или удалите модули, отвечающие за управление модулями.',
	'ADD_MODULE'						=> 'Добавить модуль',
	'ADD_MODULE_CONFIRM'				=> 'Вы уверены, что хотите добавить выбранный модуль с указанным методом использования?',
	'ADD_MODULE_TITLE'					=> 'Добавление модуля',

	'CANNOT_REMOVE_MODULE'		=> 'Невозможно удалить модуль, имеющий дочерние модули. Удалите или переместите все дочерние модули перед выполнением этой операции.',
	'CATEGORY'					=> 'Раздел',
	'CHOOSE_MODE'				=> 'Выбор метода использования',
	'CHOOSE_MODE_EXPLAIN'		=> 'Выберите метод использования модулей.',
	'CHOOSE_MODULE'				=> 'Выбор модуля',
	'CHOOSE_MODULE_EXPLAIN'		=> 'Выберите файл, вызываемый данным модулем.',
	'CREATE_MODULE'				=> 'Создать новый модуль',

	'DEACTIVATED_MODULE'		=> 'Деактивировать модуль',
	'DELETE_MODULE'				=> 'Удалить модуль',
	'DELETE_MODULE_CONFIRM'		=> 'Вы уверены, что хотите удалить этот модуль?',

	'EDIT_MODULE'				=> 'Редактирование модуля',
	'EDIT_MODULE_EXPLAIN'		=> 'Здесь Вы можете ввести установки модуля',

	'HIDDEN_MODULE'				=> 'Скрытый модуль',

	'MODULE'						=> 'Модуль',
	'MODULE_ADDED'					=> 'Модуль успешно добавлен.',
	'MODULE_DELETED'				=> 'Модуль успешно удален.',
	'MODULE_DISPLAYED'				=> 'Отображение модуля',
	'MODULE_DISPLAYED_EXPLAIN'		=> 'Если Вы не хотите, чтобы модуль отображался в списке, но хотите его использовать, установите переключатель в положение "нет".',
	'MODULE_EDITED'					=> 'Модуль успешно отредактирован.',
	'MODULE_ENABLED'				=> 'Модуль доступен',
	'MODULE_LANGNAME'				=> 'Имя модуля',
	'MODULE_LANGNAME_EXPLAIN'		=> 'Введите отображаемое имя модуля. Используйте имя переменной, если имя модуля объявлено в языковом файле.',
	'MODULE_TYPE'					=> 'Тип модуля',

	'NO_CATEGORY_TO_MODULE'		=> 'Невозможно объявить раздел модулем. Удалите/переместите все дочерние модули перед выполнением этой операции.',
	'NO_MODULE'					=> 'Модуль не найден.',
	'NO_MODULE_ID'				=> 'Не определен ID модуля.',
	'NO_MODULE_LANGNAME'		=> 'Не определено имя модуля.',
	'NO_PARENT'					=> 'Нет родителя',

	'PARENT'					=> 'Родитель',
	'PARENT_NO_EXIST'			=> 'Родитель не существует.',

	'SELECT_MODULE'				=> 'Выберите модуль',
));

?>