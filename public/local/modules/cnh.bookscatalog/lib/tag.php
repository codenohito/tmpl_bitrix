<?php

namespace Cnh\BooksCatalog;

use Bitrix\Main\Entity;

class TagTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'cnh_books_catalog_tag';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\StringField('NAME')
		);
	}
}
