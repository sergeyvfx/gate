<?php
/**
*
* acp common [Russian]
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

// Common
$lang = array_merge($lang, array(
	'ACP_ADMINISTRATORS'			=> 'Администраторы',
	'ACP_ADMIN_LOGS'				=> 'Лог администраторов',
	'ACP_ADMIN_ROLES'				=> 'Администраторские роли',
	'ACP_ATTACHMENTS'				=> 'Вложения',
	'ACP_ATTACHMENT_SETTINGS'		=> 'Настройки вложений',
	'ACP_AUTH_SETTINGS'				=> 'Аутентификация',
	'ACP_AUTOMATION'				=> 'Автоматизация',
	'ACP_AVATAR_SETTINGS'			=> 'Аватары',

	'ACP_BACKUP'					=> 'Резервное копирование',
	'ACP_BAN'						=> 'Блокировка доступа',
	'ACP_BAN_EMAILS'				=> 'Черный список e-mail',
	'ACP_BAN_IPS'					=> 'Черный список IP',
	'ACP_BAN_USERNAMES'				=> 'Черный список пользователей',
	'ACP_BBCODES'					=> 'BBCodes',
	'ACP_BOARD_CONFIGURATION'		=> 'Конфигурация',
	'ACP_BOARD_FEATURES'			=> 'Функции форума',
	'ACP_BOARD_MANAGEMENT'			=> 'Управление форумом',
	'ACP_BOARD_SETTINGS'			=> 'Настройки форума',
	'ACP_BOTS'						=> 'Поисковые боты',

	'ACP_CAPTCHA'					=> 'Визуальное подтверждение',

	'ACP_CAT_DATABASE'				=> 'База данных',
	'ACP_CAT_DOT_MODS'				=> 'Модули',
	'ACP_CAT_FORUMS'				=> 'Форумы',
	'ACP_CAT_GENERAL'				=> 'Общие',
	'ACP_CAT_MAINTENANCE'			=> 'Обслуживание',
	'ACP_CAT_PERMISSIONS'			=> 'Права доступа',
	'ACP_CAT_POSTING'				=> 'Сообщения',
	'ACP_CAT_STYLES'				=> 'Стили',
	'ACP_CAT_SYSTEM'				=> 'Система',
	'ACP_CAT_USERGROUP'				=> 'Пользователи и группы',
	'ACP_CAT_USERS'					=> 'Пользователи',
	'ACP_CLIENT_COMMUNICATION'		=> 'Средства связи',
	'ACP_COOKIE_SETTINGS'			=> 'Настройки cookies',
	'ACP_CRITICAL_LOGS'				=> 'Лог ошибок',
	'ACP_CUSTOM_PROFILE_FIELDS'		=> 'Дополнительные поля в профиле',

	'ACP_DATABASE'					=> 'База данных',
	'ACP_DISALLOW'					=> 'Запрещенные имена',
	'ACP_DISALLOW_USERNAMES'		=> 'Запрещенные имена',

	'ACP_EMAIL_SETTINGS'			=> 'Настройки почты',
	'ACP_EXTENSION_GROUPS'			=> 'Группы расширений',

	'ACP_FORUM_BASED_PERMISSIONS'		=> 'Локальные права доступа',
	'ACP_FORUM_LOGS'					=> 'Логи',
	'ACP_FORUM_MANAGEMENT'				=> 'Форумы',
	'ACP_FORUM_MODERATORS'				=> 'Модераторы форумов',
	'ACP_FORUM_PERMISSIONS'				=> 'Доступ к форумам',
	'ACP_FORUM_PERMISSIONS_COPY'		=> 'Копирование прав доступа',
	'ACP_FORUM_ROLES'					=> 'Форумные роли',

	'ACP_GENERAL_CONFIGURATION'			=> 'Общие настройки',
	'ACP_GENERAL_TASKS'					=> 'Общие задачи',
	'ACP_GLOBAL_MODERATORS'				=> 'Главные модераторы',
	'ACP_GLOBAL_PERMISSIONS'			=> 'Глобальные права доступа',
	'ACP_GROUPS'						=> 'Группы',
	'ACP_GROUPS_FORUM_PERMISSIONS'		=> 'Форумные права групп',
	'ACP_GROUPS_MANAGE'					=> 'Управление группами',
	'ACP_GROUPS_MANAGEMENT'				=> 'Группы',
	'ACP_GROUPS_PERMISSIONS'			=> 'Права групп',

	'ACP_ICONS'						=> 'Значки тем',
	'ACP_ICONS_SMILIES'				=> 'Значки тем и смайлики',
	'ACP_IMAGESETS'					=> 'Наборы изображений',
	'ACP_INACTIVE_USERS'			=> 'Неактивные пользователи',
	'ACP_INDEX'						=> 'Главная страница',

	'ACP_JABBER_SETTINGS'			=> 'Настройки Jabber',

	'ACP_LANGUAGE'					=> 'Языки',
	'ACP_LANGUAGE_PACKS'			=> 'Языковые пакеты',
	'ACP_LOAD_SETTINGS'				=> 'Нагрузка на сервер',
	'ACP_LOGGING'					=> 'Логи',

	'ACP_MAIN'						=> 'Главная страница центра администрирования',
	'ACP_MANAGE_EXTENSIONS'			=> 'Управление расширениями',
	'ACP_MANAGE_FORUMS'				=> 'Управление форумами',
	'ACP_MANAGE_RANKS'				=> 'Управление званиями',
	'ACP_MANAGE_REASONS'			=> 'Управление жалобами',
	'ACP_MANAGE_USERS'				=> 'Управление пользователями',
	'ACP_MASS_EMAIL'				=> 'Массовая рассылка почты',
	'ACP_MESSAGES'					=> 'Сообщения',
	'ACP_MESSAGE_SETTINGS'			=> 'Личные сообщения',
	'ACP_MODULE_MANAGEMENT'			=> 'Управление модулями',
	'ACP_MOD_LOGS'					=> 'Лог модераторов',
	'ACP_MOD_ROLES'					=> 'Модераторские роли',

	'ACP_NO_ITEMS'					=> 'Элементы отсутствуют.',

	'ACP_ORPHAN_ATTACHMENTS'		=> 'Потерянные вложения',

	'ACP_PERMISSIONS'				=> 'Права доступа',
	'ACP_PERMISSION_MASKS'			=> 'Маски прав доступа',
	'ACP_PERMISSION_ROLES'			=> 'Роли',
	'ACP_PERMISSION_TRACE'			=> 'Трассировка прав доступа',
	'ACP_PHP_INFO'					=> 'Сведения о PHP',
	'ACP_POST_SETTINGS'				=> 'Размещение сообщений',
	'ACP_PRUNE_FORUMS'				=> 'Чистка форумов',
	'ACP_PRUNE_USERS'				=> 'Удаление пользователей',
	'ACP_PRUNING'					=> 'Чистка',

	'ACP_QUICK_ACCESS'				=> 'Быстрый доступ',

	'ACP_RANKS'						=> 'Звания',
	'ACP_REASONS'					=> 'Управление жалобами',
	'ACP_REGISTER_SETTINGS'			=> 'Регистрация пользователей',

	'ACP_RESTORE'					=> 'Восстановление',

	'ACP_FEED'					=> 'Управление каналами новостей',
	'ACP_FEED_SETTINGS'			=> 'Каналы новостей',

	'ACP_SEARCH'					=> 'Настройки поиска',
	'ACP_SEARCH_INDEX'				=> 'Поисковые индексы',
	'ACP_SEARCH_SETTINGS'			=> 'Поиск',

	'ACP_SECURITY_SETTINGS'			=> 'Безопасность',
	'ACP_SEND_STATISTICS'			=> 'Статистическая информация',
	'ACP_SERVER_CONFIGURATION'		=> 'Конфигурация сервера',
	'ACP_SERVER_SETTINGS'			=> 'Настройки сервера',
	'ACP_SIGNATURE_SETTINGS'		=> 'Подписи',
	'ACP_SMILIES'					=> 'Смайлики',
	'ACP_STYLE_COMPONENTS'			=> 'Компоненты стилей',
	'ACP_STYLE_MANAGEMENT'			=> 'Управление стилями',
	'ACP_STYLES'					=> 'Стили',

	'ACP_SUBMIT_CHANGES'			=> 'Подтвердить изменения',

	'ACP_TEMPLATES'					=> 'Шаблоны',
	'ACP_THEMES'					=> 'Темы',

	'ACP_UPDATE'						=> 'Обновление',
	'ACP_USERS_FORUM_PERMISSIONS'		=> 'Форумные права пользователей',
	'ACP_USERS_LOGS'					=> 'Лог пользователей',
	'ACP_USERS_PERMISSIONS'				=> 'Права пользователей',
	'ACP_USER_ATTACH'					=> 'Вложения',
	'ACP_USER_AVATAR'					=> 'Аватар',
	'ACP_USER_FEEDBACK'					=> 'Заметки о пользователе',
	'ACP_USER_GROUPS'					=> 'Группы',
	'ACP_USER_MANAGEMENT'				=> 'Пользователи',
	'ACP_USER_OVERVIEW'					=> 'Обзор',
	'ACP_USER_PERM'						=> 'Права доступа',
	'ACP_USER_PREFS'					=> 'Личные настройки',
	'ACP_USER_PROFILE'					=> 'Профиль',
	'ACP_USER_RANK'						=> 'Звание',
	'ACP_USER_ROLES'					=> 'Пользовательские роли',
	'ACP_USER_SECURITY'					=> 'Блокировка пользователей',
	'ACP_USER_SIG'						=> 'Подпись',
	'ACP_USER_WARNINGS'					=> 'Предупреждения',

	'ACP_VC_SETTINGS'						=> 'Средства против спам-ботов',
	'ACP_VC_CAPTCHA_DISPLAY'				=> 'Просмотр визуального подтверждения',
	'ACP_VERSION_CHECK'						=> 'Проверка обновлений',
	'ACP_VIEW_ADMIN_PERMISSIONS'			=> 'Права администраторов',
	'ACP_VIEW_FORUM_MOD_PERMISSIONS'		=> 'Права модераторов',
	'ACP_VIEW_FORUM_PERMISSIONS'			=> 'Локальные права',
	'ACP_VIEW_GLOBAL_MOD_PERMISSIONS'		=> 'Права главного модератора',
	'ACP_VIEW_USER_PERMISSIONS'				=> 'Права пользователей',

	'ACP_WORDS'						=> 'Автоцензор',

	'ACTION'					=> 'Операция',
	'ACTIONS'					=> 'Операции',
	'ACTIVATE'					=> 'Включить',
	'ADD'						=> 'Добавить',
	'ADMIN'						=> 'Администрирование',
	'ADMIN_INDEX'				=> 'Главная',
	'ADMIN_PANEL'				=> 'Центр администрирования',

    'ADM_LOGOUT'                => 'Выход&nbsp;из&nbsp;ACP',
	'ADM_LOGGED_OUT'			=> 'Вы успешно вышли из центра администрирования',

	'BACK'						=> 'Назад',

	'COLOUR_SWATCH'				=> 'Цветовая палитра',
	'CONFIG_UPDATED'			=> 'Конфигурация успешно обновлена.',

	'DEACTIVATE'					=> 'Отключить',
	'DIRECTORY_DOES_NOT_EXIST'		=> 'Указанный путь не существует «%s» не существует.',
	'DIRECTORY_NOT_DIR'				=> 'Указанный путь «%s» не является папкой.',
	'DIRECTORY_NOT_WRITABLE'		=> 'Запись в папку «%s» запрещена.',
	'DISABLE'						=> 'Отключить',
	'DOWNLOAD'						=> 'Скачать',
	'DOWNLOAD_AS'					=> 'Скачать как',
	'DOWNLOAD_STORE'				=> 'Скачать или сохранить на сервере',
	'DOWNLOAD_STORE_EXPLAIN'		=> 'Вы можете напрямую скачать файл или сохранить его на сервере в папке <samp>store/</samp>.',

	'EDIT'						=> 'Изменить',
	'ENABLE'					=> 'Включить',
	'EXPORT_DOWNLOAD'			=> 'Скачать',
	'EXPORT_STORE'				=> 'Сохранить',

	'GENERAL_OPTIONS'			=> 'Общие параметры',
	'GENERAL_SETTINGS'			=> 'Общие настройки',
	'GLOBAL_MASK'				=> 'Глобальная маска доступа',

	'INSTALL'					=> 'Установить',
	'IP'						=> 'IP-адрес пользователя',
	'IP_HOSTNAME'				=> 'IP-адреса или хосты',

	'LOGGED_IN_AS'				=> 'Вы вошли как:',
	'LOGIN_ADMIN'				=> 'Для входа в центр администрирования вы должны быть авторизованы.',
	'LOGIN_ADMIN_CONFIRM'		=> 'Для входа в центр администрирования необходимо повторно ввести свое имя и пароль.',
	'LOGIN_ADMIN_SUCCESS'		=> 'Проверка подлинности прошла успешно. Сейчас вы будете перенаправлены в центр администрирования.',
	'LOOK_UP_FORUM'				=> 'Выберите форум',
	'LOOK_UP_FORUMS_EXPLAIN'=> 'Можно выбрать более одного форума.',

	'MANAGE'					=> 'Управление',
	'MENU_TOGGLE'				=> 'Показать или скрыть боковое меню',
	'MORE'						=> 'Подробнее',			// Not used at the moment
	'MORE_INFORMATION'			=> 'Дополнительная информация »',
	'MOVE_DOWN'					=> 'Вниз',
	'MOVE_UP'					=> 'Вверх',

	'NOTIFY'					=> 'Уведомление',
	'NO_ADMIN'				=> 'У Вас нет прав для доступа в центр администрирования.',
	'NO_EMAILS_DEFINED'			=> 'Не найдено правильных адресов электронной почты.',
	'NO_PASSWORD_SUPPLIED'		=> 'Необходимо ввести пароль для получения доступа в центр администрирования.',

	'OFF'						=> 'Отключено',
	'ON'						=> 'Включено',

	'PARSE_BBCODE'							=> 'Разрешить BBCode',
	'PARSE_SMILIES'							=> 'Разрешить смайлики',
	'PARSE_URLS'							=> 'Разрешить ссылки',
	'PERMISSIONS_TRANSFERRED'				=> 'Передача прав доступа',
	'PERMISSIONS_TRANSFERRED_EXPLAIN'		=> 'В настоящее время Вы имеете права пользователя %1$s. Вы можете просматривать форумы с этими правами, но не будете иметь доступа к центру администраторирования, поскольку права администратора не были переданы. Вы можете <a href="%2$s"><strong>вернуть свои права</strong></a> в любое время.',	
	'PROCEED_TO_ACP'						=> '%sПерейти в центр администрирования%s',

	'REMIND'								=> 'Напомнить',
	'RESYNC'								=> 'Синхронизировать',
	'RETURN_TO'								=> 'Вернуться в…',

	'SELECT_ANONYMOUS'			=> 'Выбрать аккаунт гостя',
	'SELECT_OPTION'				=> 'Выберите операцию',

	'SETTING_TOO_LOW'			=> 'Введённое значение параметра «%1$s» слишком мало. Минимальное допустимое значение - %2$d.',
	'SETTING_TOO_BIG'			=> 'Введённое значение параметра «%1$s» слишком велико. Максимальное допустимое значение - %2$d.',
	'SETTING_TOO_LONG'			=> 'Введённое значение параметра «%1$s» слишком длинное. Максимальная допустимая длина - %2$d.',
	'SETTING_TOO_SHORT'			=> 'Введённое значение параметра «%1$s» слишком короткое. Минимальная допустимая длина - %2$d.',

	'SHOW_ALL_OPERATIONS'		=> 'Показать все операции',

	'UCP'						=> 'Центр пользователя',
	'USERNAMES_EXPLAIN'			=> 'Вводите каждое имя на новой строке.',
	'USER_CONTROL_PANEL'		=> 'Центр пользователя',

	'WARNING'					=> 'Предупреждение',
));

// PHP info
$lang = array_merge($lang, array(
	'ACP_PHP_INFO_EXPLAIN'		=> 'Эта страница отображает сведения о версии PHP, установленной на данном сервере. Она включает подробные сведения о загруженных модулях, доступных переменных и настройках по умолчанию. Эта информация может быть полезной при диагностировании различных неполадок. Некоторые хостинг-компании ограничивают отображение этой информации по соображениям безопасности. Не предоставляйте никаких сведений с этой страницы никому, кроме <a href="http://www.phpbb.com/about/team/">официальных членов группы поддержки</a>.',

	'NO_PHPINFO_AVAILABLE'		=> 'Информация о конфигурации PHP недоступна. Функция phpinfo() отключена по соображениям безопасности.',
));

// Logs
$lang = array_merge($lang, array(
	'ACP_ADMIN_LOGS_EXPLAIN'		=> 'Это список операций, выполненных администраторами форума. Вы можете сортировать список по имени пользователя, дате, IP-адресу или по операции. При наличии необходимых прав Вы можете удалить отдельные записи или очистить весь лог целиком.',
	'ACP_CRITICAL_LOGS_EXPLAIN'		=> 'Это список операций, выполненных самим форумом. Этот журнал предоставляет информацию, которую можно использовать для решения некоторых проблем. Например, с доставкой электронных сообщений. Вы можете сортировать список по имени пользователя, дате, IP-адресу или по операции. При наличии необходимых прав Вы можете удалить отдельные записи или очистить весь лог целиком.',
	'ACP_MOD_LOGS_EXPLAIN'			=> 'Это список всех операций, выполненных модераторами в форумах, темах и сообщениях. Вы можете сортировать список по имени пользователя, дате, IP-адресу или по операции. При наличии необходимых прав Вы можете удалить отдельные записи или очистить весь лог целиком.',
	'ACP_USERS_LOGS_EXPLAIN'		=> 'Это список операций, выполненных пользователями или над пользователями (жалобы, предупреждения и заметки о пользователях).',
	'ALL_ENTRIES'					=> 'Все записи',

	'DISPLAY_LOG'		=> 'Показать записи за',

	'NO_ENTRIES'		=> 'Нет записей в указанный период.',

	'SORT_IP'			=> 'IP-адрес',
	'SORT_DATE'			=> 'Дата',
	'SORT_ACTION'		=> 'Операция',
));

// Index page
$lang = array_merge($lang, array(
	'ADMIN_INTRO'					=> 'Благодарим за выбор phpBB в качестве решения для создания форума. Данная страница предоставляет краткий обзор различной статистки форума. Ссылки слева позволяют управлять всеми аспектами работы Вашего форума. Каждая страница содержит инструкции по использованию текущего инструмента.',
	'ADMIN_LOG'						=> 'Операции администрации',
	'ADMIN_LOG_INDEX_EXPLAIN'		=> 'Ниже находится список пяти последних операций, выполненных администраторами форума. Полную копию журнала можно просмотреть, выбрав соответствующую ссылку в меню, или нажав ссылку ниже «Лог администраторов».',
	'AVATAR_DIR_SIZE'				=> 'Размер папки с аватарами',

	'BOARD_STARTED'			=> 'Дата запуска форума',
	'BOARD_VERSION'			=> 'Версия форума',

	'DATABASE_SERVER_INFO'		=> 'Сервер базы данных',
	'DATABASE_SIZE'				=> 'Размер базы данных',
	
	
	// Enviroment configuration checks, mbstring related
	'ERROR_MBSTRING_FUNC_OVERLOAD'					=> 'Перегрузка функций настроена неверно',
	'ERROR_MBSTRING_FUNC_OVERLOAD_EXPLAIN'			=> '<var>mbstring.func_overload</var> должен быть установлен 0 или 4. Вы можете проверить текущее значение на <samp>PHP information</samp> page.',
	'ERROR_MBSTRING_ENCODING_TRANSLATION'			=> 'Transparent character encoding is improperly configured',
	'ERROR_MBSTRING_ENCODING_TRANSLATION_EXPLAIN'	=> '<var>mbstring.encoding_translation</var> должен быть установлен 0. Вы можете проверить текущее значение на <samp>PHP information</samp> page.',
	'ERROR_MBSTRING_HTTP_INPUT'						=> 'HTTP ввод преобразования символов настроен неправильно',
	'ERROR_MBSTRING_HTTP_INPUT_EXPLAIN'				=> '<var>mbstring.http_input</var> должно быть тоже самое, что и <samp>pass</samp>. Вы можете проверить текущее значение на <samp>PHP information</samp> page.',
	'ERROR_MBSTRING_HTTP_OUTPUT'					=> 'HTTP вывод преобразования символов настроен неправильно',
	'ERROR_MBSTRING_HTTP_OUTPUT_EXPLAIN'			=> '<var>mbstring.http_output</var> должен быть установлен <samp>pass</samp>. Вы можете проверить текущее значение на <samp>PHP information</samp> page.',


	'FILES_PER_DAY'			=> 'Вложений в день',
	'FORUM_STATS'			=> 'Статистика форума',

	'GZIP_COMPRESSION'		=> 'Сжатие GZip',

	'NOT_AVAILABLE'			=> 'Недоступно',
	'NUMBER_FILES'			=> 'Вложений',
	'NUMBER_POSTS'			=> 'Сообщений',
	'NUMBER_TOPICS'			=> 'Тем',
	'NUMBER_USERS'			=> 'Пользователей',
	'NUMBER_ORPHAN'			=> 'Потерянных вложений',

	'PHP_VERSION_OLD'		=> 'Версия PHP, которая используется на данном сервере, устарела и не будет поддерживаться будущими версиями phpBB.',

	'POSTS_PER_DAY'			=> 'Сообщений в день',

	'PURGE_CACHE'				=> 'Очистить кэш',
	'PURGE_CACHE_CONFIRM'		=> 'Вы действительно хотите очистить кэш?',
	'PURGE_CACHE_EXPLAIN'		=> 'Очистка всех кэшированных элементов (кэшированные файлы шаблонов и запросов).',

	'PURGE_SESSIONS'			=> 'Удалить все сессии',
	'PURGE_SESSIONS_CONFIRM'	=> 'Вы действительно хотите удалить все текущие сессии?',
	'PURGE_SESSIONS_EXPLAIN'	=> 'Удаление всех сессий. Сессии всех пользователей, находящихся в данный момент на форуме, будут завершены',

	'RESET_DATE'						=> 'Сброс даты запуска форума',
	'RESET_DATE_CONFIRM'				=> 'Вы действительно хотите сбросить дату запуска форума?',
	'RESET_ONLINE'						=> 'Сброс наибольшего количества посетителей',
	'RESET_ONLINE_CONFIRM'				=> 'Вы действительно хотите сбросить счетчик наибольшего количества посетителей на форуме?',
	'RESYNC_POSTCOUNTS'					=> 'Синхронизировать счетчики сообщений',
	'RESYNC_POSTCOUNTS_EXPLAIN'			=> 'Будут учтены только существующие сообщения. Удаленные сообщения не будут подсчитаны.',
	'RESYNC_POSTCOUNTS_CONFIRM'			=> 'Вы действительно хотите синхронизировать счетчики сообщений?',
	'RESYNC_POST_MARKING'				=> 'Синхронизировать свои темы',
	'RESYNC_POST_MARKING_CONFIRM'		=> 'Вы действительно хотите синхронизировать свои темы?',
	'RESYNC_POST_MARKING_EXPLAIN'		=> 'Снятие пометок со всех тем с последующей корректной маркировкой всех тем, к которым был доступ в последние шесть месяцев.',
	'RESYNC_STATS'						=> 'Синхронизировать статистику',
	'RESYNC_STATS_CONFIRM'				=> 'Вы действительно хотите синхронизировать статистику?',
	'RESYNC_STATS_EXPLAIN'				=> 'Пересчет общего количества сообщений, тем, пользователей и файлов.',
	'RUN'								=> 'Выполнить',

	'STATISTIC'						=> 'Статистика',
	'STATISTIC_RESYNC_OPTIONS'		=> 'Синхронизация или сброс статистики',

	'TOPICS_PER_DAY'		=> 'Тем в день',

	'UPLOAD_DIR_SIZE'		=> 'Размер всех вложений',
	'USERS_PER_DAY'			=> 'Пользователей в день',

	'VALUE'						=> 'Значение',
	'VERSIONCHECK_FAIL'			=> 'Не удалось получить сведения о последней версии.',
	'VERSIONCHECK_FORCE_UPDATE'	=> 'Проверить ещё раз',
	'VIEW_ADMIN_LOG'			=> 'Лог администраторов',
	'VIEW_INACTIVE_USERS'		=> 'Неактивные пользователи',

	'WELCOME_PHPBB'				=> 'Добро пожаловать в phpBB',
	'WRITABLE_CONFIG'			=> 'Ваш файл конфигурации (config.php) открыт для перезаписи. Мы настоятельно рекомендуем Вам изменить права доступа к файлу на 640, или хотябы на 644 (например: <a href="http://ru.wikipedia.org/wiki/Chmod" rel="external">chmod</a> 640 config.php).',
));

// Inactive Users
$lang = array_merge($lang, array(
	'INACTIVE_DATE'						=> 'Дата деактивации',
	'INACTIVE_REASON'					=> 'Причина',
	'INACTIVE_REASON_MANUAL'			=> 'Аккаунт отключен администратором',
	'INACTIVE_REASON_PROFILE'			=> 'Изменен профиль',
	'INACTIVE_REASON_REGISTER'			=> 'Новый аккаунт',
	'INACTIVE_REASON_REMIND'			=> 'Принудительная повторная активация',
	'INACTIVE_REASON_UNKNOWN'			=> 'Неизвестно',
	'INACTIVE_USERS'					=> 'Неактивные пользователи',
	'INACTIVE_USERS_EXPLAIN'			=> 'Ниже находится список зарегистрированных пользователей, аккаунты которых неактивны. Вы можете активировать, удалить или отправить неактивным пользователям электронное сообщение с напоминанием об активации.',
	'INACTIVE_USERS_EXPLAIN_INDEX'		=> 'Ниже находится список десяти последних зарегистрированных пользователей, имеющих неактивные аккаунты. Полный список неактивных пользователей можно просмотреть, выбрав соответствующую ссылку в меню, или нажав ссылку ниже «Неактивные пользователи». На странице «Неактивные пользователи» Вы можете активировать, удалить или отправить неактивным пользователям электронное сообщение с напоминанием об активации.',

	'NO_INACTIVE_USERS'		=> 'Нет неактивных пользователей',

	'SORT_INACTIVE'			=> 'Дата деактивации',
	'SORT_LAST_VISIT'		=> 'Последнее посещение',
	'SORT_REASON'			=> 'Причина',
	'SORT_REG_DATE'			=> 'Дата регистрации',
	'SORT_LAST_REMINDER'	=> 'Последнее напоминание',
	'SORT_REMINDER'			=> 'Дата напоминания',

	'USER_IS_INACTIVE'			=> 'Пользователь неактивен',
));

// Send statistics page
$lang = array_merge($lang, array(
	'EXPLAIN_SEND_STATISTICS'	=> 'Пожалуйста, отправьте информацию о своём сервере и форуме на phpbb.com для статистического анализа. Все данные полностью <strong>анонимны</strong>. Основываясь на предоставленной Вами информации, мы будем принимать решения о функциональности будущих версий phpBB. Статистические данные представляются публично. Мы также используем эти данные совместно с проектом PHP (язык программирования, на котором написан phpBB).',
	'EXPLAIN_SHOW_STATISTICS'	=> 'Используя кнопку ниже, Вы можете просмотреть все переменные, которые будут переданы',
	'DONT_SEND_STATISTICS'		=> 'Вернуться в Центр администратора, если Вы не хотите отправлять статистическую информацию.',
	'GO_ACP_MAIN'				=> 'Перейти на главную страницу Центра администратора',
	'HIDE_STATISTICS'			=> 'Скрыть подробности',
	'SEND_STATISTICS'			=> 'Отправить статистическую информацию',
	'SHOW_STATISTICS'			=> 'Показать подробности',
	'THANKS_SEND_STATISTICS'	=> 'Благодарим за предоставленную информацию!',
));

// Log Entries
$lang = array_merge($lang, array(
	'LOG_ACL_ADD_USER_GLOBAL_U_'			=> '<strong>Добавлены или изменены пользовательские права пользователей</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_U_'			=> '<strong>Добавлены или изменены пользовательские права групп</strong><br />» %s',
	'LOG_ACL_ADD_USER_GLOBAL_M_'			=> '<strong>Добавлены или изменены права главного модератора пользователей</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_M_'			=> '<strong>Добавлены или изменены права главного модератора групп</strong><br />» %s',
	'LOG_ACL_ADD_USER_GLOBAL_A_'			=> '<strong>Добавлены или изменены администраторские права пользователей</strong><br />» %s',
	'LOG_ACL_ADD_GROUP_GLOBAL_A_'			=> '<strong>Добавлены или изменены администраторские права групп</strong><br />» %s',

	'LOG_ACL_ADD_ADMIN_GLOBAL_A_'			=> '<strong>Добавлены или изменены администраторы</strong><br />» %s',
	'LOG_ACL_ADD_MOD_GLOBAL_M_'				=> '<strong>Добавлены или изменены главные модераторы</strong><br />» %s',

	'LOG_ACL_ADD_USER_LOCAL_F_'				=> '<strong>Добавлен или изменен доступ пользователей к форуму</strong> из %1$s<br />» %2$s',
	'LOG_ACL_ADD_USER_LOCAL_M_'				=> '<strong>Добавлен или изменен доступ модератора к форуму</strong> из %1$s<br />» %2$s',
	'LOG_ACL_ADD_GROUP_LOCAL_F_'			=> '<strong>Добавлен или изменен доступ групп к форуму</strong> из %1$s<br />» %2$s',
	'LOG_ACL_ADD_GROUP_LOCAL_M_'			=> '<strong>Добавлен или изменен модераторский доступ групп к форуму</strong> из %1$s<br />» %2$s',

	'LOG_ACL_ADD_MOD_LOCAL_M_'				=> '<strong>Добавлены или изменены модераторы</strong> из %1$s<br />» %2$s',
	'LOG_ACL_ADD_FORUM_LOCAL_F_'			=> '<strong>Добавлен или изменен доступ к форуму</strong> из %1$s<br />» %2$s',

	'LOG_ACL_DEL_ADMIN_GLOBAL_A_'			=> '<strong>Удалены администраторы</strong><br />» %s',
	'LOG_ACL_DEL_MOD_GLOBAL_M_'				=> '<strong>Удалены главные модераторы</strong><br />» %s',
	'LOG_ACL_DEL_MOD_LOCAL_M_'				=> '<strong>Удалены модераторы</strong> из %1$s<br />» %2$s',
	'LOG_ACL_DEL_FORUM_LOCAL_F_'			=> '<strong>Удален доступ пользователя/группы к форуму</strong> из %1$s<br />» %2$s',

	'LOG_ACL_TRANSFER_PERMISSIONS'			=> '<strong>Перенесены права доступа пользователя</strong><br />» %s',
	'LOG_ACL_RESTORE_PERMISSIONS'			=> '<strong>Возвращены свои права доступа после использования прав пользователя</strong><br />» %s',

	'LOG_ADMIN_AUTH_FAIL'			=> '<strong>Неудачная попытка входа в качестве администратора</strong>',
	'LOG_ADMIN_AUTH_SUCCESS'		=> '<strong>Успешный вход в качестве администратора</strong>',

	'LOG_ATTACHMENTS_DELETED'		=> '<strong>Удалены вложения пользователя</strong><br />» %s',

	'LOG_ATTACH_EXT_ADD'			=> '<strong>Добавлено или изменено расширение вложений</strong><br />» %s',
	'LOG_ATTACH_EXT_DEL'			=> '<strong>Удалено расширение вложений</strong><br />» %s',
	'LOG_ATTACH_EXT_UPDATE'			=> '<strong>Обновлено расширение вложений</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_ADD'		=> '<strong>Добавлена группа расширений</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_EDIT'		=> '<strong>Изменена группа расширений</strong><br />» %s',
	'LOG_ATTACH_EXTGROUP_DEL'		=> '<strong>Удалена группа расширений</strong><br />» %s',
	'LOG_ATTACH_FILEUPLOAD'			=> '<strong>Невложенный файл прикреплен к сообщению</strong><br />» ID %1$d — %2$s',
	'LOG_ATTACH_ORPHAN_DEL'			=> '<strong>Невложенные файлы удалены</strong><br />» %s',

	'LOG_BAN_EXCLUDE_USER'		=> '<strong>Исключен пользователь из списка заблокированных</strong> по причине "<em>%1$s</em>"<br />» %2$s ',
	'LOG_BAN_EXCLUDE_IP'		=> '<strong>Исключен IP-адрес из списка заблокированных</strong> по причине "<em>%1$s</em>"<br />» %2$s ',
	'LOG_BAN_EXCLUDE_EMAIL'		=> '<strong>Исключен e-mail адрес из списка заблокированных</strong> по причине "<em>%1$s</em>"<br />» %2$s ',
	'LOG_BAN_USER'				=> '<strong>Заблокирован пользователь</strong> по причине "<em>%1$s</em>"<br />» %2$s ',
	'LOG_BAN_IP'				=> '<strong>Заблокирован IP-адрес</strong> по причине "<em>%1$s</em>"<br />» %2$s',
	'LOG_BAN_EMAIL'				=> '<strong>Заблокирован e-mail адрес</strong> по причине "<em>%1$s</em>"<br />» %2$s',
	'LOG_UNBAN_USER'			=> '<strong>Разблокирован пользователь</strong><br />» %s',
	'LOG_UNBAN_IP'				=> '<strong>Разблокирован IP-адрес</strong><br />» %s',
	'LOG_UNBAN_EMAIL'			=> '<strong>Разблокирован e-mail адрес</strong><br />» %s',

	'LOG_BBCODE_ADD'			=> '<strong>Добавлен новый BBCode</strong><br />» %s',
	'LOG_BBCODE_EDIT'			=> '<strong>Изменен BBCode</strong><br />» %s',
	'LOG_BBCODE_DELETE'			=> '<strong>Удален BBCode</strong><br />» %s',

	'LOG_BOT_ADDED'			=> '<strong>Добавлен новый бот</strong><br />» %s',
	'LOG_BOT_DELETE'		=> '<strong>Удален бот</strong><br />» %s',
	'LOG_BOT_UPDATED'		=> '<strong>Обновлен существующий бот</strong><br />» %s',

	'LOG_CLEAR_ADMIN'			=> '<strong>Очищен лог администратора</strong>',
	'LOG_CLEAR_CRITICAL'		=> '<strong>Очищен лог ошибок</strong>',
	'LOG_CLEAR_MOD'				=> '<strong>Очищен лог модератора</strong>',
	'LOG_CLEAR_USER'			=> '<strong>Очищен лог пользователя</strong><br />» %s',
	'LOG_CLEAR_USERS'			=> '<strong>Очищены логи пользователей</strong>',

	'LOG_CONFIG_ATTACH'				=> '<strong>Изменены настройки вложений</strong>',
	'LOG_CONFIG_AUTH'				=> '<strong>Изменены настройки аутентификации</strong>',
	'LOG_CONFIG_AVATAR'				=> '<strong>Изменены настройки аватар</strong>',
	'LOG_CONFIG_COOKIE'				=> '<strong>Изменены настройки cookies</strong>',
	'LOG_CONFIG_EMAIL'				=> '<strong>Изменены настройки почты</strong>',
	'LOG_CONFIG_FEATURES'			=> '<strong>Изменены возможности форума</strong>',
	'LOG_CONFIG_LOAD'				=> '<strong>Изменены настройки нагрузки на сервер</strong>',
	'LOG_CONFIG_MESSAGE'			=> '<strong>Изменены настройки личных сообщений</strong>',
	'LOG_CONFIG_POST'				=> '<strong>Изменены настройки сообщений</strong>',
	'LOG_CONFIG_REGISTRATION'		=> '<strong>Изменены настройки регистрации пользователей</strong>',
	'LOG_CONFIG_FEED'				=> '<strong>Изменены настройки каналов новостей</strong>',
	'LOG_CONFIG_SEARCH'				=> '<strong>Изменены настройки поиска</strong>',
	'LOG_CONFIG_SECURITY'			=> '<strong>Изменены настройки безопасности</strong>',
	'LOG_CONFIG_SERVER'				=> '<strong>Изменены настройки сервера</strong>',
	'LOG_CONFIG_SETTINGS'			=> '<strong>Изменены настройки форума</strong>',
	'LOG_CONFIG_SIGNATURE'			=> '<strong>Изменены настройки подписей</strong>',
	'LOG_CONFIG_VISUAL'				=> '<strong>Изменены настройки визуального подтверждения</strong>',

	'LOG_APPROVE_TOPIC'				=> '<strong>Одобрена тема</strong><br />» %s',
	'LOG_BUMP_TOPIC'				=> '<strong>Поднята тема</strong><br />» %s',
	'LOG_DELETE_POST'				=> '<strong>Удалено сообщение</strong><br />» %s',
	'LOG_DELETE_SHADOW_TOPIC'		=> '<strong>Удаленная скрытая тема</strong><br />» %s',
	'LOG_DELETE_TOPIC'				=> '<strong>Удаленная тема</strong><br />» %s',
	'LOG_FORK'						=> '<strong>Скопированная тема</strong><br />» из %s',
	'LOG_LOCK'						=> '<strong>Зарытая тема</strong><br />» %s',
	'LOG_LOCK_POST'					=> '<strong>Закрытое сообщение</strong><br />» %s',
	'LOG_MERGE'						=> '<strong>Объединенные сообщения</strong> в теме %1$s to %2$s',
	'LOG_MOVE'						=> '<strong>Перемещена тема</strong><br />» из %1$s в %2$s',
	'LOG_PM_REPORT_CLOSED'			=> '<strong>Закрыта жалоба на ЛС</strong><br />» %s',
	'LOG_PM_REPORT_DELETED'			=> '<strong>Удалена жалоба на ЛС</strong><br />» %s',
	'LOG_POST_APPROVED'				=> '<strong>Одобрение сообщения</strong><br />» %s',
	'LOG_POST_DISAPPROVED'			=> '<strong>Отклонение сообщения «%1$s» по следующей причине</strong><br />» %2$s',
	'LOG_POST_EDITED'				=> '<strong>Редактирование сообщения в теме «%1$s», написанного автором</strong><br />» %2$s',
	'LOG_REPORT_CLOSED'				=> '<strong>Закрытие жалобы</strong><br />» %s',
	'LOG_REPORT_DELETED'			=> '<strong>Удаление жалобы</strong><br />» %s',
	'LOG_SPLIT_DESTINATION'			=> '<strong>Перемещены разделённые сообщения</strong><br />» в %s',
	'LOG_SPLIT_SOURCE'				=> '<strong>Разделены сообщения</strong><br />» из %s',

	'LOG_TOPIC_APPROVED'			=> '<strong>Одобрение темы</strong><br />» %s',
	'LOG_TOPIC_DISAPPROVED'			=> '<strong>Отклонение темы «%1$s» по следующий причине</strong><br />%2$s',
	'LOG_TOPIC_RESYNC'				=> '<strong>Синхронизированы счетчики темы</strong><br />» %s',
	'LOG_TOPIC_TYPE_CHANGED'		=> '<strong>Изменен тип темы</strong><br />» %s',
	'LOG_UNLOCK'					=> '<strong>Открыта тема</strong><br />» %s',
	'LOG_UNLOCK_POST'				=> '<strong>Разблокировано сообщение</strong><br />» %s',

	'LOG_DISALLOW_ADD'			=> '<strong>Запрещено имя</strong><br />» %s',
	'LOG_DISALLOW_DELETE'		=> '<strong>Разрешено имя</strong>',

	'LOG_DB_BACKUP'				=> '<strong>Резервное копирование базы данных</strong>',
	'LOG_DB_DELETE'				=> '<strong>Удаление резервной копии базы данных</strong>',
	'LOG_DB_RESTORE'			=> '<strong>Восстановление базы данных</strong>',

	'LOG_DOWNLOAD_EXCLUDE_IP'		=> '<strong>Исключен IP-адрес/хост из списка скачивания</strong><br />» %s',
	'LOG_DOWNLOAD_IP'				=> '<strong>Добавлен IP-адрес/хост в список скачивания</strong><br />» %s',
	'LOG_DOWNLOAD_REMOVE_IP'		=> '<strong>Удален IP-адрес/хост из списка скачивания</strong><br />» %s',

	'LOG_ERROR_JABBER'			=> '<strong>Ошибка Jabber</strong><br />» %s',
	'LOG_ERROR_EMAIL'			=> '<strong>Ошибка почты</strong><br />» %s',

	'LOG_FORUM_ADD'								=> '<strong>Создан форум</strong><br />» %s',
	'LOG_FORUM_COPIED_PERMISSIONS'			=> '<strong>Скопированы настройки форума</strong> из %1$s<br /> в %2$s',
	'LOG_FORUM_DEL_FORUM'						=> '<strong>Удален форум</strong><br />» %s',
	'LOG_FORUM_DEL_FORUMS'						=> '<strong>Удален форум с подфорумами</strong><br />» %s',
	'LOG_FORUM_DEL_MOVE_FORUMS'					=> '<strong>Удален форум и перемещены подфорумы</strong> в %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS'					=> '<strong>Удален форум и перемещены сообщения</strong> в %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS_FORUMS'			=> '<strong>Удален форум с подфорумами, перемещены сообщения</strong> в %1$s<br />» %2$s',
	'LOG_FORUM_DEL_MOVE_POSTS_MOVE_FORUMS'		=> '<strong>Удален форум, перемещены сообщения</strong> в %1$s <strong>и подфорумы</strong> в %2$s<br />» %3$s',
	'LOG_FORUM_DEL_POSTS'						=> '<strong>Удален форум с сообщениями</strong><br />» %s',
	'LOG_FORUM_DEL_POSTS_FORUMS'				=> '<strong>Удален форум с сообщениями и подфорумами</strong><br />» %s',
	'LOG_FORUM_DEL_POSTS_MOVE_FORUMS'			=> '<strong>Удален форум с сообщениями, перемещены подфорумы</strong> в %1$s<br />» %2$s',
	'LOG_FORUM_EDIT'							=> '<strong>Изменена информация о форуме</strong><br />» %s',
	'LOG_FORUM_MOVE_DOWN'						=> '<strong>Перемещен форум</strong> %1$s <strong>под</strong> %2$s',
	'LOG_FORUM_MOVE_UP'							=> '<strong>Перемещен форум</strong> %1$s <strong>над</strong> %2$s',
	'LOG_FORUM_SYNC'							=> '<strong>Синхронизирован форум</strong><br />» %s',

	'LOG_GENERAL_ERROR'							=> '<strong>Произошла общая ошибка</strong>: %1$s <br />» %2$s',

	'LOG_GROUP_CREATED'			=> '<strong>Создана группа пользователей</strong><br />» %s',
	'LOG_GROUP_DEFAULTS'		=> '<strong>Назначена группой по умолчанию для пользователей</strong><br />» %s',
	'LOG_GROUP_DELETE'			=> '<strong>Удалена группа</strong><br />» %s',
	'LOG_GROUP_DEMOTED'			=> '<strong>Сняты администраторы в группе</strong> %1$s<br />» %2$s',
	'LOG_GROUP_PROMOTED'		=> '<strong>Назначены администраторы в группе</strong> %1$s<br />» %2$s',
	'LOG_GROUP_REMOVE'			=> '<strong>Удалены пользователи из группы</strong> %1$s<br />» %2$s',
	'LOG_GROUP_UPDATED'			=> '<strong>Обновлена информация о группе</strong><br />» %s',
	'LOG_MODS_ADDED'			=> '<strong>Добавлены новые администраторы в группу</strong> %1$s<br />» %2$s',
	'LOG_USERS_ADDED'			=> '<strong>Добавлены пользователи в группу</strong> %1$s<br />» %2$s',
	'LOG_USERS_APPROVED'		=> '<strong>Одобрены кандидаты в группу</strong> %1$s<br />» %2$s',
	'LOG_USERS_PENDING'			=> '<strong>Пользователи подали запрос на вступление в группу «%1$s» и ожидают одобрения</strong><br />» %2$s',

	'LOG_IMAGE_GENERATION_ERROR'	=> '<strong>Ошибка при создании изображения</strong><br />» Ошибка в %1$s в строке %2$s: %3$s',

	'LOG_IMAGESET_ADD_DB'				=> '<strong>Добавлен пакет изображений в базу данных</strong><br />» %s',
	'LOG_IMAGESET_ADD_FS'				=> '<strong>Добавлен пакет изображений на сервер</strong><br />» %s',
	'LOG_IMAGESET_DELETE'				=> '<strong>Удален пакет изображений</strong><br />» %s',
	'LOG_IMAGESET_EDIT_DETAILS'			=> '<strong>Изменена информация о пакете изображений</strong><br />» %s',
	'LOG_IMAGESET_EDIT'					=> '<strong>Изменен пакет изображений</strong><br />» %s',
	'LOG_IMAGESET_EXPORT'				=> '<strong>Экспортирован пакет изображений</strong><br />» %s',
	'LOG_IMAGESET_LANG_MISSING'			=> '<strong>Отсутствует локализация «%2$s» для набора рисунков</strong><br />» «%1$s»',
	'LOG_IMAGESET_LANG_REFRESHED'		=> '<strong>Обновлена локализация набора рисунков «%2$s»</strong><br />» %1$s',
	'LOG_IMAGESET_REFRESHED'			=> '<strong>Обновлен набор рисунков</strong><br />» %s',

	'LOG_INACTIVE_ACTIVATE'		=> '<strong>Активированы неактивные пользователи</strong><br />» %s',
	'LOG_INACTIVE_DELETE'		=> '<strong>Удалены неактивные пользователи</strong><br />» %s',
	'LOG_INACTIVE_REMIND'		=> '<strong>Отправлены e-mail напоминания неактивным пользователям</strong><br />» %s',
	'LOG_INSTALL_CONVERTED'		=> '<strong>Форум %1$s сконвертирована в phpBB %2$s</strong>',
	'LOG_INSTALL_INSTALLED'		=> '<strong>Установлен phpBB %s</strong>',

	'LOG_IP_BROWSER_FORWARDED_CHECK'		=> '<strong>Неудачная проверка сессии по IP-адресу/браузеру/X_FORWARDED_FOR</strong><br />»IP-адрес пользователя "<em>%1$s</em>" проверен по IP-адресу сессии "<em>%2$s</em>", строка браузера пользователя "<em>%3$s</em>" проверена по строке браузера сессии "<em>%4$s</em>" и строка X_FORWARDED_FOR пользователя "<em>%5$s</em>" проверена по строке X_FORWARDED_FOR сессии "<em>%6$s</em>".',

	'LOG_JAB_CHANGED'				=> '<strong>Изменен аккаунт Jabber</strong>',
	'LOG_JAB_PASSCHG'				=> '<strong>Изменен пароль Jabber</strong>',
	'LOG_JAB_REGISTER'				=> '<strong>Зарегистрирован аккаунт Jabber</strong>',
	'LOG_JAB_SETTINGS_CHANGED'		=> '<strong>Изменены настройки Jabber</strong>',

	'LOG_LANGUAGE_PACK_DELETED'			=> '<strong>Удален языковой пакет</strong><br />» %s',
	'LOG_LANGUAGE_PACK_INSTALLED'		=> '<strong>Установлен языковой пакет</strong><br />» %s',
	'LOG_LANGUAGE_PACK_UPDATED'			=> '<strong>Обновлена информация о языковом пакете</strong><br />» %s',
	'LOG_LANGUAGE_FILE_REPLACED'		=> '<strong>Заменен языковой файл</strong><br />» %s',
	'LOG_LANGUAGE_FILE_SUBMITTED'		=> '<strong>Отправлен языковых файл и помещен в папку store</strong><br />» %s',

	'LOG_MASS_EMAIL'			=> '<strong>Произведена массовая рассылка почты</strong><br />» %s',

	'LOG_MCP_CHANGE_POSTER'		=> '<strong>Изменен автор темы "%1$s"</strong><br />» с %2$s на %3$s',

	'LOG_MODULE_DISABLE'		=> '<strong>Отключен модуль</strong><br />» %s',
	'LOG_MODULE_ENABLE'			=> '<strong>Включен модуль</strong><br />» %s',
	'LOG_MODULE_MOVE_DOWN'		=> '<strong>Перемещение модуля</strong><br />» «%1$s» под «%2$s»',
	'LOG_MODULE_MOVE_UP'		=> '<strong>Перемещение модуля</strong><br />» «%1$s» над «%2$s»',
	'LOG_MODULE_REMOVED'		=> '<strong>Удален модуль</strong><br />» %s',
	'LOG_MODULE_ADD'			=> '<strong>Добавлен модуль</strong><br />» %s',
	'LOG_MODULE_EDIT'			=> '<strong>Изменен модуль</strong><br />» %s',

	'LOG_A_ROLE_ADD'			=> '<strong>Добавлена администраторская роль</strong><br />» %s',
	'LOG_A_ROLE_EDIT'			=> '<strong>Изменена администраторская роль</strong><br />» %s',
	'LOG_A_ROLE_REMOVED'		=> '<strong>Удалена администраторская роль</strong><br />» %s',
	'LOG_F_ROLE_ADD'			=> '<strong>Добавлена форумная роль</strong><br />» %s',
	'LOG_F_ROLE_EDIT'			=> '<strong>Изменена форумная роль</strong><br />» %s',
	'LOG_F_ROLE_REMOVED'		=> '<strong>Удалена форумная роль</strong><br />» %s',
	'LOG_M_ROLE_ADD'			=> '<strong>Добавлена модераторская роль</strong><br />» %s',
	'LOG_M_ROLE_EDIT'			=> '<strong>Изменена модераторская роль</strong><br />» %s',
	'LOG_M_ROLE_REMOVED'		=> '<strong>Удалена модераторская роль</strong><br />» %s',
	'LOG_U_ROLE_ADD'			=> '<strong>Добавлена пользовательская роль</strong><br />» %s',
	'LOG_U_ROLE_EDIT'			=> '<strong>Изменена пользовательская роль</strong><br />» %s',
	'LOG_U_ROLE_REMOVED'		=> '<strong>Удалена пользовательская роль</strong><br />» %s',

	'LOG_PROFILE_FIELD_ACTIVATE'		=> '<strong>Активировано поле профиля</strong><br />» %s',
	'LOG_PROFILE_FIELD_CREATE'			=> '<strong>Добавлено поле профиля</strong><br />» %s',
	'LOG_PROFILE_FIELD_DEACTIVATE'		=> '<strong>Деактивировано поле профиля</strong><br />» %s',
	'LOG_PROFILE_FIELD_EDIT'			=> '<strong>Изменено поле профиля</strong><br />» %s',
	'LOG_PROFILE_FIELD_REMOVED'			=> '<strong>Удалено поле профиля</strong><br />» %s',

	'LOG_PRUNE'						=> '<strong>Очищены форумы</strong><br />» %s',
	'LOG_AUTO_PRUNE'				=> '<strong>Произведена автоочистка форумов</strong><br />» %s',
	'LOG_PRUNE_USER_DEAC'			=> '<strong>Деактивированы пользователи</strong><br />» %s',
	'LOG_PRUNE_USER_DEL_DEL'		=> '<strong>Удалены пользователи с их сообщениями</strong><br />» %s',
	'LOG_PRUNE_USER_DEL_ANON'		=> '<strong>Удалены пользователи, их сообщения оставлены</strong><br />» %s',

	'LOG_PURGE_CACHE'				=> '<strong>Очистка кэша</strong>',
	'LOG_PURGE_SESSIONS'			=> '<strong>Удаление сессий</strong>',

	'LOG_RANK_ADDED'			=> '<strong>Добавлено звание</strong><br />» %s',
	'LOG_RANK_REMOVED'			=> '<strong>Удалено звание</strong><br />» %s',
	'LOG_RANK_UPDATED'			=> '<strong>Обновлено звание</strong><br />» %s',

	'LOG_REASON_ADDED'			=> '<strong>Добавлена причина жалобы/отклонения</strong><br />» %s',
	'LOG_REASON_REMOVED'		=> '<strong>Удалена причина жалобы/отклонения</strong><br />» %s',
	'LOG_REASON_UPDATED'		=> '<strong>Обновлена причина жалобы/отклонения</strong><br />» %s',

	'LOG_REFERER_INVALID'			=> '<strong>Проверка рефёрера не удалась </strong><br />»Рефёрер: “<em>%1$s</em>”. Запрос был отклонён, сессия удалена.',
	'LOG_RESET_DATE'				=> '<strong>Сброшена дата запуска форума</strong>',
	'LOG_RESET_ONLINE'				=> '<strong>Сброшен рекорд посещаемости</strong>',
	'LOG_RESYNC_POSTCOUNTS'			=> '<strong>Синхронизированы счетчики сообщений пользователей</strong>',
	'LOG_RESYNC_POST_MARKING'		=> '<strong>Синхронизированы свои темы</strong>',
	'LOG_RESYNC_STATS'				=> '<strong>Синхронизирована статистика сообщений, тем и пользователей</strong>',

	'LOG_SEARCH_INDEX_CREATED'		=> '<strong>Созданы поисковые индексы для</strong><br />» %s',
	'LOG_SEARCH_INDEX_REMOVED'		=> '<strong>Удалены поисковые индексы для</strong><br />» %s',
	'LOG_STYLE_ADD'					=> '<strong>Добавлен стиль</strong><br />» %s',
	'LOG_STYLE_DELETE'				=> '<strong>Удален стиль</strong><br />» %s',
	'LOG_STYLE_EDIT_DETAILS'		=> '<strong>Изменен стиль</strong><br />» %s',
	'LOG_STYLE_EXPORT'				=> '<strong>Экспортирован стиль</strong><br />» %s',

	'LOG_TEMPLATE_ADD_DB'				=> '<strong>Добавлен шаблон в базу данных</strong><br />» %s',
	'LOG_TEMPLATE_ADD_FS'				=> '<strong>Добавлен шаблон на сервер</strong><br />» %s',
	'LOG_TEMPLATE_CACHE_CLEARED'		=> '<strong>Удалена кэшированная версия файлов шаблона <em>%1$s</em></strong><br />» %2$s',
	'LOG_TEMPLATE_DELETE'				=> '<strong>Удален шаблон</strong><br />» %s',
	'LOG_TEMPLATE_EDIT'					=> '<strong>Изменен шаблон <em>%1$s</em></strong><br />» %2$s',
	'LOG_TEMPLATE_EDIT_DETAILS'			=> '<strong>Изменена информация о шаблоне</strong><br />» %s',
	'LOG_TEMPLATE_EXPORT'				=> '<strong>Экспортирован шаблон</strong><br />» %s',
	'LOG_TEMPLATE_REFRESHED'			=> '<strong>Обновлен шаблон</strong><br />» %s',

	'LOG_THEME_ADD_DB'				=> '<strong>Добавлена тема в базу данных</strong><br />» %s',
	'LOG_THEME_ADD_FS'				=> '<strong>Добавлена тема на сервер</strong><br />» %s',
	'LOG_THEME_DELETE'				=> '<strong>Удалена тема</strong><br />» %s',
	'LOG_THEME_EDIT_DETAILS'		=> '<strong>Изменена информация о теме</strong><br />» %s',
	'LOG_THEME_EDIT'				=> '<strong>Изменена тема <em>%1$s</em></strong><br />» Изменен класс <em>%2$s</em>',
	'LOG_THEME_EDIT_FILE'			=> '<strong>Изменение темы оформления стиля <em>%1$s</em></strong><br />» Изменен файл <em>%2$s</em>',
	'LOG_THEME_EXPORT'				=> '<strong>Экспортирована тема</strong><br />» %s',
	'LOG_THEME_REFRESHED'			=> '<strong>Обновлена тема</strong><br />» %s',

	'LOG_UPDATE_DATABASE'		=> '<strong>Обновлена база данных с версии %1$s до версии %2$s</strong>',
	'LOG_UPDATE_PHPBB'			=> '<strong>Обновлен phpBB с версии %1$s до версии %2$s</strong>',

	'LOG_USER_ACTIVE'			=> '<strong>Активирован пользователь</strong><br />» %s',
	'LOG_USER_BAN_USER'			=> '<strong>Заблокирован пользователь через раздел Управление пользователями</strong> по причине "<em>%1$s</em>"<br />» %2$s',
	'LOG_USER_BAN_IP'			=> '<strong>Заблокирован IP-адрес через раздел Управление пользователями</strong> по причине "<em>%1$s</em>"<br />» %2$s',
	'LOG_USER_BAN_EMAIL'		=> '<strong>Заблокирован e-mail адрес через раздел Управление пользователями</strong> по причине "<em>%1$s</em>"<br />» %2$s',
	'LOG_USER_DELETED'			=> '<strong>Удален пользователь</strong><br />» %s',
	'LOG_USER_DEL_ATTACH'		=> '<strong>Удалены все вложения пользователя</strong><br />» %s',
	'LOG_USER_DEL_AVATAR'		=> '<strong>Удален аватар пользователя</strong><br />» %s',
	'LOG_USER_DEL_OUTBOX'		=> '<strong>Очищена папка «Исходящие» пользователя</strong><br />» %s',
	'LOG_USER_DEL_POSTS'		=> '<strong>Удалены все сообщения пользователя</strong><br />» %s',
	'LOG_USER_DEL_SIG'			=> '<strong>Удалена подпись пользователя</strong><br />» %s',
	'LOG_USER_INACTIVE'			=> '<strong>Деактивирован пользователь</strong><br />» %s',
	'LOG_USER_MOVE_POSTS'		=> '<strong>Перемещены сообщения пользователя</strong><br />» «%1$s» в форум «%2$s»',
	'LOG_USER_NEW_PASSWORD'		=> '<strong>Изменен пароль пользователя</strong><br />» %s',
	'LOG_USER_REACTIVATE'		=> '<strong>Принудительная повторная активация аккаунта пользователя</strong><br />» %s',
	'LOG_USER_REMOVED_NR'		=> '<strong>Удалён флаг новой регистрации пользователя</strong><br />» %s',

	'LOG_USER_UPDATE_EMAIL'		=> '<strong>Пользователем "%1$s" изменен e-mail адрес</strong><br />» с «%2$s» на «%3$s»"',
	'LOG_USER_UPDATE_NAME'		=> '<strong>Изменено имя пользователя</strong><br />» с «%1$s» на «%2$s»',
	'LOG_USER_USER_UPDATE'		=> '<strong>Обновлена информация о пользователе</strong><br />» %s',

	'LOG_USER_ACTIVE_USER'			=> '<strong>Активирован аккаунт пользователя</strong>',
	'LOG_USER_DEL_AVATAR_USER'		=> '<strong>Удален аватар пользователя</strong>',
	'LOG_USER_DEL_SIG_USER'			=> '<strong>Удалена подпись пользователя</strong>',
	'LOG_USER_FEEDBACK'				=> '<strong>Добавлена заметка о пользователе</strong><br />» %s',
	'LOG_USER_GENERAL'				=> '<strong>Добавлен элемент:</strong><br />» %s',
	'LOG_USER_INACTIVE_USER'		=> '<strong>Деактивирован аккаунт пользователя</strong>',
	'LOG_USER_LOCK'					=> '<strong>Пользователь закрыл свою тему</strong><br />» %s',
	'LOG_USER_MOVE_POSTS_USER'		=> '<strong>Перемещены все сообщения в форум "%s"</strong>',
	'LOG_USER_REACTIVATE_USER'		=> '<strong>Принудительная повторная активация аккаунта пользователя</strong>',
	'LOG_USER_UNLOCK'				=> '<strong>Пользователь открыл свою тему</strong><br />» %s',
	'LOG_USER_WARNING'				=> '<strong>Вынесено предупреждение пользователю</strong><br />» %s',
	'LOG_USER_WARNING_BODY'			=> '<strong>Пользователю вынесено следующее предупреждение</strong><br />» %s',

	'LOG_USER_GROUP_CHANGE'				=> '<strong>Пользователь изменил группу по умолчанию</strong><br />» %s',
	'LOG_USER_GROUP_DEMOTE'				=> '<strong>Пользователь снят с администраторов группы</strong><br />» %s',
	'LOG_USER_GROUP_JOIN'				=> '<strong>Пользователь вступил в группу</strong><br />» %s',
	'LOG_USER_GROUP_JOIN_PENDING'		=> '<strong>Пользователь отправил запрос на вступление в группу и должен быть одобрен</strong><br />» %s',
	'LOG_USER_GROUP_RESIGN'				=> '<strong>Пользователь отказался от членства в группе</strong><br />» %s',

	'LOG_WARNING_DELETED'		=> '<strong>Удалено предупреждение у пользователя</strong><br />» %s',
	'LOG_WARNINGS_DELETED'		=> '<strong>Удалено %2$s предупреждений у пользователя</strong><br />» %1$s', // Example: '<strong>Deleted 2 user warnings</strong><br />» username'
	'LOG_WARNINGS_DELETED_ALL'	=> '<strong>Удалены все предупреждения у пользователя</strong><br />» %s',

	'LOG_WORD_ADD'				=> '<strong>Добавлен цензор слов</strong><br />» %s',
	'LOG_WORD_DELETE'			=> '<strong>Удален цензор слов</strong><br />» %s',
	'LOG_WORD_EDIT'				=> '<strong>Изменен цензор слов</strong><br />» %s',
));

?>