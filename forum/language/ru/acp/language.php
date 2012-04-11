<?php
/**
*
* acp_language [Russian]
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
	'ACP_FILES'							=> 'Управление языковыми файлами',
	'ACP_LANGUAGE_PACKS_EXPLAIN'		=> 'Здесь Вы можете устанавливать/удалять языковые пакеты. Языковой пакет, который установлен по умолчанию, помечен звёздочкой (*).',

	'EMAIL_FILES'				=> 'Шаблоны e-mail сообщений',

	'FILE_CONTENTS'					=> 'Содержимое файла',
	'FILE_FROM_STORAGE'				=> 'Файл из папки хранения',

	'HELP_FILES'					=> 'Файлы помощи',

	'INSTALLED_LANGUAGE_PACKS'		=> 'Установленные языковые пакеты',
	'INVALID_LANGUAGE_PACK'			=> 'Выбранный языковой пакет, скорее всего, поврежден. Проверьте языковой пакет и загрузите его на сервер повторно, если необходимо.',
	'INVALID_UPLOAD_METHOD'			=> 'Выбранный метод загрузки на сервер недоступен, выберите другой метод.',

	'LANGUAGE_DETAILS_UPDATED'				=> 'Языковая информация успешно обновлена.',
	'LANGUAGE_ENTRIES'						=> 'Языковые данные',
	'LANGUAGE_ENTRIES_EXPLAIN'				=> 'Здесь Вы можете изменять существующие или пока непереведенные записи в файлах языкового пакета.<br /><strong>Примечание:</strong> Если Вы изменили языковой файл, изменения будут сохранены в отдельной папке для последующего скачивания. Изменения не будут видны Вашим пользователям до тех пор, пока вы не замените исходные языковые файлы на сервере (загрузив новые).',
	'LANGUAGE_FILES'						=> 'Языковые файлы',
	'LANGUAGE_KEY'							=> 'Ключ языка',
	'LANGUAGE_PACK_ALREADY_INSTALLED'		=> 'Этот языковой пакет уже установлен.',
	'LANGUAGE_PACK_DELETED'					=> 'Языковой пакет <strong>%s</strong> был успешно удален. Все пользователи, использующие его, были переведены на язык форума по умолчанию.',
	'LANGUAGE_PACK_DETAILS'					=> 'Информация о языковом пакете',
	'LANGUAGE_PACK_INSTALLED'				=> 'Языковой пакет <strong>%s</strong> был успешно установлен.',
	'LANGUAGE_PACK_CPF_UPDATE'			=> 'Установлен язык по умолчанию. Если есть необходимость вы можете его изменить.',
	'LANGUAGE_PACK_ISO'						=> 'ISO',
	'LANGUAGE_PACK_LOCALNAME'				=> 'Местное название',
	'LANGUAGE_PACK_NAME'					=> 'Название',
	'LANGUAGE_PACK_NOT_EXIST'				=> 'Выбранный языковой пакет не существует.',
	'LANGUAGE_PACK_USED_BY'					=> 'Используют (включая роботов)',
	'LANGUAGE_VARIABLE'						=> 'Языковая переменная',
	'LANG_AUTHOR'							=> 'Автор языкового пакета',
	'LANG_ENGLISH_NAME'						=> 'Имя на английском',
	'LANG_ISO_CODE'							=> 'Код ISO',
	'LANG_LOCAL_NAME'						=> 'Местное название',

	'MISSING_LANGUAGE_FILE'			=> 'Отсутствует языковой файл: <strong style="color:red">%s</strong>',
	'MISSING_LANG_VARIABLES'		=> 'Отсутствуют языковые переменные',
	'MODS_FILES'					=> 'Языковые файлы модов',

	'NO_FILE_SELECTED'					=> 'Вы не указали языковой файл.',
	'NO_LANG_ID'						=> 'Вы не указали языковой пакет.',
	'NO_REMOVE_DEFAULT_LANG'			=> 'Вы не можете удалить языковой пакет, используемый по умолчанию.<br />Если Вы хотите удалить этот языковой пакет, сначала измените язык форума по умолчанию.',
	'NO_UNINSTALLED_LANGUAGE_PACKS'		=> 'Все языковые пакеты установлены',

	'REMOVE_FROM_STORAGE_FOLDER'			=> 'Удалить из папки хранения',

	'SELECT_DOWNLOAD_FORMAT'		=> 'Выбрать формат скачивания',
	'SUBMIT_AND_DOWNLOAD'			=> 'Отправить форму и скачать файл',
	'SUBMIT_AND_UPLOAD'				=> 'Отправить форму и загрузить файл на сервер',

	'THOSE_MISSING_LANG_FILES'				=> 'Следующие языковые файлы отсутствуют в папке языка %s',
	'THOSE_MISSING_LANG_VARIABLES'			=> 'Следующие языковые переменные отсутствуют в языковом пакете <strong>%s</strong>',

	'UNINSTALLED_LANGUAGE_PACKS'		=> 'Доступные для установки языковые пакеты',

	'UNABLE_TO_WRITE_FILE'			=> 'Не удалось записать файл в %s.',
	'UPLOAD_COMPLETED'				=> 'Загрузка на сервер успешно завершена.',
	'UPLOAD_FAILED'					=> 'Загрузка на сервер не удалась. Может потребоваться заменить соответствующий файл вручную.',
	'UPLOAD_METHOD'					=> 'Метод загрузки на сервер',
	'UPLOAD_SETTINGS'				=> 'Настройки загрузки на сервер',

	'WRONG_LANGUAGE_FILE'			=> 'Выбранный языковой файл поврежден или не существует.',
));

?>