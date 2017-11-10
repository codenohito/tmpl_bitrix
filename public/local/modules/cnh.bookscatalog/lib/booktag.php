<?php

namespace Cnh\BooksCatalog;

use Bitrix\Main\Entity;

class BookTagTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'cnh_books_catalog_book_to_tag';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('BOOK_ID', array(
                'primary' => true
            )),
            new Entity\ReferenceField(
                'BOOK',
                'Cnh\BooksCatalog\Book',
                array('=this.BOOK_ID' => 'ref.ID')
            ),
            new Entity\IntegerField('TAG_ID', array(
                'primary' => true
            )),
            new Entity\ReferenceField(
                'TAG',
                'Cnh\BooksCatalog\Tag',
                array('=this.TAG_ID' => 'ref.ID')
            )
        );
    }
}
