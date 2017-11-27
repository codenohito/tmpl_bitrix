<?php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/menu.php
// https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=5187

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

// if(!$APPLICATION->GetGroupRight("cnh.bookscatalog")>"D") {
// 	return false;
// }

// if(!CModule::IncludeModule('cnh.bookscatalog'))
// {
// 	return false;
// }

$menu = array(
	array(
		'parent_menu' => 'global_menu_content',
		'sort' => 400,
		'text' => Loc::getMessage('CHN_BOOKSCATALOG_MENU_TITLE'),
		'title' => Loc::getMessage('CHN_BOOKSCATALOG_MENU_TITLE'),
		'url' => '',
		'module_id' => 'cnh.bookscatalog',
		'items_id' => 'menu_bookscatalog',
		'items' => array(
			array(
				'text' => Loc::getMessage('CHN_BOOKSCATALOG_MENU_AUTHORS_TITLE'),
				'title' => Loc::getMessage('CHN_BOOKSCATALOG_MENU_AUTHORS_TITLE'),
				'url' => 'bookscatalog_authors_list.php?lang=' . LANGUAGE_ID,
				'more_url' => array(
					'bookscatalog_authors_list.php',
					'bookscatalog_authors_show.php'
				)
			),
			array(
				'text' => Loc::getMessage('CHN_BOOKSCATALOG_MENU_BOOKS_TITLE'),
				'title' => Loc::getMessage('CHN_BOOKSCATALOG_MENU_BOOKS_TITLE'),
				'url' => 'bookscatalog_books_list.php?lang=' . LANGUAGE_ID,
				'more_url' => array(
					'bookscatalog_books_list.php',
					'bookscatalog_books_show.php'
				)
			)
		)
	)
);

return $menu;
