<?php
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Class cnh_bookscatalog extends CModule
{
	var $MODULE_ID = "cnh.bookscatalog";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS;
	var $MODULE_CSS;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function cnh_bookscatalog()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path . '/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->MODULE_NAME = Loc::getMessage('CHN_BOOKSCATALOG_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('CHN_BOOKSCATALOG_MODULE_DESCRIPTION');

		$this->MODULE_GROUP_RIGHTS = "N";

		$this->PARTNER_NAME = Loc::getMessage('CHN_BOOKSCATALOG_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('CHN_BOOKSCATALOG_PARTNER_URI');
	}

	function installFiles()
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/local/modules/cnh.bookscatalog/install/admin',
		             $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		return true;
	}

	function uninstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/local/modules/cnh.bookscatalog/install/admin/',
		               $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		return true;
	}

	function installDB()
	{
		global $DB, $APPLICATION;
		$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/local/modules/cnh.bookscatalog/install/db/'.strtolower($DB->type).'/install.sql');
		if (is_array($errors))
		{
			$APPLICATION->ThrowException(implode(' ', $errors));
			return false;
		}
		// - Другой способ исталляции базы данных — использование метода createDbTable.
		// - Но этот способ закрывает возможность удобного отслеживания изменений в структуре базе данных.
		// - Когда у тебя есть файл с sql кодом, всегда можно посмотреть изменения в нём и накатить их вручную.
		// - Это очень полезно, когда происходит доработка уже используемой системы.
		// if (Loader::includeModule($this->MODULE_ID))
		// {
		// 	Cnh\BooksCatalog\BookTable::getEntity()->createDbTable();
		// }
		return true;
	}

	function uninstallDB()
	{
		global $DB, $APPLICATION;
		$errors = false;
		$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/local/modules/cnh.bookscatalog/install/db/'.strtolower($DB->type).'/uninstall.sql');
		if (is_array($errors))
		{
			$APPLICATION->ThrowException(implode(' ', $errors));
			return false;
		}
		//- Другой способ: dropTable таблицы каждого нашего DataManager'а.
		//- См. комментарий в installDB, который объясняет, почему это не самый удобный способ.
		// if (Loader::includeModule($this->MODULE_ID))
		// {
		// 	$connection = Application::getInstance()->getConnection();
		// 	$connection->dropTable(Cnh\BooksCatalog\BookTable::getTableName());
		// }
		return true;
	}

	function DoInstall()
	{
		$this->installFiles();
		$this->installDB();
		ModuleManager::registerModule($this->MODULE_ID);
		// $APPLICATION->IncludeAdminFile("Установка модуля cnh_bookscatalog", $DOCUMENT_ROOT."/bitrix/modules/cnh.bookscatalog/install/step.php");
	}

	function DoUninstall()
	{
		$this->uninstallDB();
		$this->uninstallFiles();
		ModuleManager::unRegisterModule($this->MODULE_ID);
		// $APPLICATION->IncludeAdminFile("Деинсталляция модуля cnh_bookscatalog", $DOCUMENT_ROOT."/bitrix/modules/cnh.bookscatalog/install/unstep.php");
	}

	// function GetModuleRightList()
	// {
	// 	global $MESS;
	// 	$arr = array(
	// 		"reference_id" => array("D","R","W"),
	// 		"reference" => array(
	// 			GetMessage("CNH_BOOKCATALOG_DENIED"),
	// 			GetMessage("CNH_BOOKCATALOG_OPENED"),
	// 			GetMessage("CNH_BOOKCATALOG_FULL")
	// 		)
	// 	);
	// 	return $arr;
	// }
}
?>
