<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

// TODO: Здесь - какой-то системный код, читающие данные и всё такое

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

// CModule::IncludeModule('cnh.bookscatalog');

echo "Welcome to admin area";
// echo "<p><pre>";
// print_r(Cnh\BooksCatalog\TagTable::getEntity()->compileDbTableStructureDump());
// echo "</pre></p>";


require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
