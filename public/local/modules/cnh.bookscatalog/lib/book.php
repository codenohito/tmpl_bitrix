<?php
namespace Cnh\BooksCatalog;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class BookTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'cnh_books_catalog_book'
	}

	public static function getUfId()
	{
		return 'THE_BOOK';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
			)),
			new Entity\StringField('ISBN',array(
				'required' => true,
				'column_name' => 'ISBNCODE',
				'validation' => function() {
					return array(
						new Entity\Validator\RegExp('/[\d-]{13,}/'),
						function ($value, $primary, $row, $field) {
							$clean = str_replace('-', '', $value);

							if (preg_match('/^.*24$/', $clean))
							{
								return true;
							}
							else
							{
								return new Entity\FieldError(
									$field,
									'Контрольная цифра ISBN не сошлась',
									'MY_ISBN_CHECKSUM'
								);
							}
						}
					);
				}
			)),
			new Entity\TextField('EDITIONS_ISBN', array(
				'serialized' => true
			))
			new Entity\StringField('TITLE'),
			new Entity\DateField('PUBLISH_DATE', array(
				'default_value' => new Type\Date
			)),
			new Entity\ExpressionField(
				'AGE_DAYS',
				'DATEDIFF(NOW(), %s)',
				array('PUBLISH_DATE')
			),
			new Entity\IntegerField('AUTHOR_ID'),
			new Entity\ReferenceField(
				'AUTHOR',
				'Cnh\BooksCatalog\Author',
				array('=this.AUTHOR_ID' => 'ref.ID'),
				array('join_type' => 'LEFT')
			)
			// new Entity\BooleanField('NAME', array(
			// 	'values' => array('N', 'Y')
			// )),
			// new Entity\EnumField('NAME', array(
			// 	'values' => array('VALUE1', 'VALUE2', 'VALUE3')
			// )),
		);
	}

	public static function onBeforeAdd(Entity\Event $event)
	{
		$result = new Entity\EventResult;
		$data = $event->getParameter("fields");

		if (isset($data['ISBN']))
		{
			$cleanIsbn = str_replace('-', '', $data['ISBN']);
			$result->modifyFields(array('ISBN' => $cleanIsbn));
		}

		return $result;
	}

	public static function onBeforeUpdate(Entity\Event $event)
	{
		$result = new Entity\EventResult;
		$data = $event->getParameter("fields");

		if (isset($data['ISBN']))
		{
			$result->addError(new Entity\FieldError(
				$event->getEntity()->getField('ISBN'),
				'Запрещено менять ISBN код у существующих книг'
			));
		}

		return $result;
	}
}

// more info at Ядро D7 -> ORM -> Концепция, описание сущности
// https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=4803

// more info at Ядро D7 -> ORM -> Операции с сущностями
// https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2244

// more info at Ядро D7 -> ORM -> Взаимосвязи между сущностями
// https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=3269&LESSON_PATH=3913.5062.5748.3269
