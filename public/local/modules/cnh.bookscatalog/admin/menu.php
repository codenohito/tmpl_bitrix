<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

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
		'items' => array(
			array(
				'text' => Loc::getMessage('CHN_BOOKSCATALOG_SUBMENU_TITLE'),
				'url' => 'bookscatalog_books_index.php?lang=' . LANGUAGE_ID,
				'more_url' => array(
					'bookscatalog_books_index.php?lang=' . LANGUAGE_ID,
					'bookscatalog_books_show.php?lang=' . LANGUAGE_ID
				),
				'title' => Loc::getMessage('CHN_BOOKSCATALOG_SUBMENU_TITLE'),
			),
		),
	),
);

return $menu;
