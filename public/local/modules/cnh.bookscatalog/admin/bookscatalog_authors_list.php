<?php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_admin.php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_admin_ex.php

use Bitrix\Main\Localization\Loc;
use Cnh\BooksCatalog\AuthorTable;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'cnh.bookscatalog');

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

Loc::loadMessages(__FILE__);

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

// CModule::IncludeModule('cnh.bookscatalog');

echo "Welcome to admin area";
// echo "<p><pre>";
// print_r(Cnh\BooksCatalog\TagTable::getEntity()->compileDbTableStructureDump());
// echo "</pre></p>";


require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
