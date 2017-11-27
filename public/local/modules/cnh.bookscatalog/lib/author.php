<?php

namespace Cnh\BooksCatalog;

use Bitrix\Main\Entity;

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
				'autocomplete' => true
			)),
			new Entity\StringField('NAME'),
			new Entity\StringField('LAST_NAME')
		);
	}
}
