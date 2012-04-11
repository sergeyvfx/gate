<?php
/**
*
* captcha_qa [Russian]
*
* @package language
* @version $Id$
* @copyright (c) 2009 phpBB Group
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
	'CAPTCHA_QA'				=> 'Текстовое подтверждение',
	'CONFIRM_QUESTION_EXPLAIN'	=> 'Этот вопрос предназначен для предотвращения автоматической отправки данных форм спам-ботами.',
	'CONFIRM_QUESTION_WRONG'	=> 'Вы дали неправильный ответ на вопрос.',

	'QUESTION_ANSWERS'			=> 'Ответы',
	'ANSWERS_EXPLAIN'			=> 'Пожалуйста, введите правильные ответы. Каждый ответ должен вводиться с новой строки.',
	'CONFIRM_QUESTION'			=> 'Вопрос',

	'ANSWER'					=> 'Ответ',
	'EDIT_QUESTION'				=> 'Редактировать вопрос',
	'QUESTIONS'					=> 'Вопрос',
	'QUESTIONS_EXPLAIN'			=> 'При отправке данных форм, для которых включено текстовое подтверждение, пользователю будет предложен один из указанных здесь вопросов. Для использования данного модуля должен быть установлен по крайней мере один вопрос на языке по умолчанию. Эти вопросы должны быть просты для вашей потенциальной аудитории, но в то же время сложны для бота, способного найти ответ через поиск в Google™. Использование обширного и часто изменяемого набора вопросов даст наилучшие результаты. Включите строгую проверку, если ответ на ваш вопрос зависит от знаков препинания, пробелов или регистра символов.',
 	'QUESTION_DELETED'			=> 'Вопрос удалён',
 	'QUESTION_LANG'				=> 'Язык',
 	'QUESTION_LANG_EXPLAIN'		=> 'Язык, на котором написаны этот вопрос и ответ на него.',
 	'QUESTION_STRICT'			=> 'Строгая проверка',
	'QUESTION_STRICT_EXPLAIN'	=> 'Включить для поддержки смешаного регистра, знаков препинания и пробелов.',

	'QUESTION_TEXT'				=> 'Вопрос',
	'QUESTION_TEXT_EXPLAIN'		=> 'Вопрос, который будет задан пользователю.',

	'QA_ERROR_MSG'				=> 'Пожалуйста, заполните все поля и введите не менее одного ответа.',
	'QA_LAST_QUESTION'			=> 'Пока плагин активирован, Вы не можете удалить все вопросы.',

));

?>