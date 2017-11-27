<?php

namespace Cnh\BooksCatalog;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AuthorTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'cnh_bookscatalog_author';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('CHN_BOOKSCATALOG_AUTHOR_ID')
			)),
			new Entity\StringField('NAME', array(
				'required' => true,
				'title' => Loc::getMessage('CHN_BOOKSCATALOG_AUTHOR_NAME')
			)),
			new Entity\StringField('LAST_NAME', array(
				'title' => Loc::getMessage('CHN_BOOKSCATALOG_AUTHOR_LAST_NAME')
			))
		);
	}
}
