<?php
// TODO look at real form module code
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile($PathInstall."/install.php");
include($PathInstall."/version.php");
if(class_exists("cnh_books_catalog")) return;
Class cnh_books_catalog extends CModule
{
	var $MODULE_ID = "cnh.bookscatalog";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	function form()
	{
		$this->MODULE_VERSION = FORM_VERSION;
		$this->MODULE_VERSION_DATE = FORM_VERSION_DATE;
		$this->MODULE_NAME = GetMessage("CHN_BOOKS_CATALOG_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("CHN_BOOKS_CATALOG_MODULE_DESCRIPTION");
	}

	function DoInstall()
	{
		global $DB, $APPLICATION, $step;
		$FORM_RIGHT = $APPLICATION->GetGroupRight("form");
		if ($FORM_RIGHT=="W")
		{
			$step = IntVal($step);
			if($step<2)
				$APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"),
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/install/step1.php");
			elseif($step==2)
				$APPLICATION->IncludeAdminFile(GetMessage("FORM_INSTALL_TITLE"),
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/install/step2.php");
		}
	}

	function DoUninstall()
	{
		global $DB, $APPLICATION, $step;
		$FORM_RIGHT = $APPLICATION->GetGroupRight("form");
		if ($FORM_RIGHT=="W")
		{
			$step = IntVal($step);
			if($step<2)
				$APPLICATION->IncludeAdminFile(GetMessage("FORM_UNINSTALL_TITLE"),
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/install/unstep1.php");
			elseif($step==2)
				$APPLICATION->IncludeAdminFile(GetMessage("FORM_UNINSTALL_TITLE"),
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/install/unstep2.php");
		}
	}

	function GetModuleRightList()
	{
		global $MESS;
		$arr = array(
			"reference_id" => array("D","R","W"),
			"reference" => array(
				GetMessage("FORM_DENIED"),
				GetMessage("FORM_OPENED"),
				GetMessage("FORM_FULL")
			)
		);
		return $arr;
	}
}
?>
