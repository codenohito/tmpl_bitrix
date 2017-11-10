<?php
namespace Cnh\Messenger;

use Bitrix\Main\Entity;

class MessageTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'cnh_messenger_message'
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID'),
			new Entity\StringField('ISBN'),
			new Entity\StringField('TITLE'),
			new Entity\DateField('PUBLISH_DATE')
		);
	}
}
?>
