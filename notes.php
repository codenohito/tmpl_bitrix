<?
   if(CModule::IncludeModule("******"))
   {
    //здесь можно использовать функции и классы модуля
   }
   ?>

<?php

// код для создания таблицы в MySQL
// (получен путем вызова BookTable::getEntity()->compileDbTableStructureDump())

// ---
$result = BookTable::add(array(
	'ISBN' => '978-0321127426',
	'TITLE' => 'Patterns of Enterprise Application Architecture',
	'PUBLISH_DATE' => new Type\Date('2002-11-16', 'Y-m-d')
));

if ($result->isSuccess())
{
	$id = $result->getId();
}
if (!$result->isSuccess())
{
	$errors = $result->getErrorMessages();

	// or

	$errors = $result->getErrors();
	foreach ($errors as $error)
	{
		if ($error->getCode() == 'MY_ISBN_CHECKSUM')
		{
			// сработал наш валидатор
		}
	}
}
// &
$result = BookTable::update($id, array(
	'PUBLISH_DATE' => new Type\Date('2002-11-15', 'Y-m-d')
));
// &
$result = BookTable::delete($id);
// https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2244&LESSON_PATH=3913.5062.5748.2244

// ---
BookTable::getList(array(
	'select'  => array(
		'ISBN',
		'TITLE',
		'PUBLICATION' => 'PUBLISH_DATE',
		new Entity\ExpressionField('MAX_AGE', 'MAX(%s)', array('AGE_DAYS')),
		'AUTHOR_NAME' => 'AUTHOR.NAME',
		'AUTHOR_LAST_NAME' => 'AUTHOR.LAST_NAME'
	),
	'filter'  => array(
		'%=TITLE' => 'Patterns%',
		array(
			'LOGIC' => 'OR',
			'=ID' => 1,
			'=ID' => 2,
		)
	),
	'group'   => array('PUBLISH_DATE'),
	'order'   => array('ID' => 'ASC'),
	'limit'   => 10,
	'offset'  => 0
));
$rows = array();
$result = BookTable::getList(array(
	...
));
while ($row = $result->fetch())
{
	$rows[] = $row;
}
// $rows = BookTable::getList($parameters)->fetchAll();
// https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=5753

// ---
\SomePartner\MyBooksCatalog\TagTable::getList(array(
	'filter' => array('=ID' => 11),
	'select' => array(
		'ID',
		'NAME',
		'BOOK_TITLE' => 'SomePartner\MyBooksCatalog\BookTag:TAG.BOOK.TITLE'
	)
));


// Тут навигация, в том числе админская
// https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2741&LESSON_PATH=3913.5062.5748.2741
?>
