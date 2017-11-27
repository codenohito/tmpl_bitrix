<?php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_admin.php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_admin_ex.php

use Bitrix\Main\Localization\Loc;
use Cnh\BooksCatalog\AuthorTable;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'cnh.bookscatalog');

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

Loc::loadMessages(__FILE__);

if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$entity = AuthorTable::getEntity();
$entity_data_class = $entity->getDataClass();
$entity_table_name = AuthorTable::getTableName();

$sTableID = 'tbl_'.$entity_table_name;
$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	'find_id',
	'find_name',
	'find_last_name'
);
function CheckFilter($FilterArr) // проверка введенных полей
{
	foreach($FilterArr as $f)
		global $$f;

	$str = "";

	if(strlen($str)>0)
	{
		global $lAdmin;
		$lAdmin->AddFilterError($str);
		return false;
	}

	return true;
}

$arFilter = Array();
$lAdmin->InitFilter($arFilterFields);
InitSorting();

if(CheckFilter($arFilterFields))
{
	if (!empty($find_id))
		$arFilter['=ID'] = $find_id;
	if (!empty($find_name))
		$arFilter['=NAME'] = $find_name;
	if (!empty($find_last_name))
		$arFilter['=LAST_NAME'] = $find_last_name;
}

if($lAdmin->EditAction())
{
	foreach($FIELDS as $ID=>$arFields)
	{
		$DB->StartTransaction();
		$ID = (int)$ID;
		if ($ID <= 0)
			continue;

		if(!$lAdmin->IsUpdated($ID))
			continue;

		// $entity_data_class::update($ID, $arFields);
		$APPLICATION->ResetException();
		if(!$entity_data_class::update($ID, $arFields))
		{
			$e = $APPLICATION->GetException();
			$lAdmin->AddUpdateError(GetMessage("SAVE_ERROR").$ID.": ".$e->GetString(), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

if($arID = $lAdmin->GroupAction())
{
	if($_REQUEST['action_target']=='selected')
	{
		$arID = array();

		$rsData = $entity_data_class::getList(array(
			"select" => array('ID'),
			"filter" => $arFilter
		));

		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}

	foreach ($arID as $ID)
	{
		$ID = (int)$ID;
		if (!$ID || $ID<=0)
			continue;

		switch($_REQUEST['action'])
		{
			case "delete":
				// if( Access::getInstance()->checkRigth__Document($ID) ) {
					if(!$entity_data_class::delete($ID))
						$lAdmin->AddGroupError(GetMessage("DELETE_ERROR"), $ID);
				// }
				break;
		}
	}
}

// $q = new Bitrix\Main\Entity\Query(AuthorTable::getEntity());
// $q->setSelect(array('*'));
// $q->setFilter($arFilter);
// echo('<p><pre>');print_r($q->getQuery());echo('</pre></p>');

$authorsList = $entity_data_class::GetList(array(
	"filter" => $arFilter,
	"order" => array($by => $order)
));
$rsData = new CAdminResult($authorsList, $sTableID);
$rsData->NavStart(20);
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES")));
$lAdmin->AddHeaders(array(
	array(
		"id"=>"ID",
		"content"=>"ID",
		"sort"=>"ID",
		"default"=>true,
		"align"=>"right"),
	array(
		"id"=>"NAME",
		"content"=>"Имя",
		"sort"=>"NAME",
		"default"=>true),
	array("id"=>"LAST_NAME",
		"content"=>"Фамилия",
		"sort"=>"LAST_NAME",
		"default"=>true)
));

while($db_res = $rsData->NavNext(true, "a_"))
{
	$row =& $lAdmin->AddRow($a_ID,$db_res);
	$row->AddField("ID", $a_ID);
	$row->AddField("NAME", $a_NAME);
	$row->AddField("LAST_NAME", $a_LAST_NAME);

	$arActions = array();
	$arActions[] = array(
		"ICON" => "edit",
		"TEXT" => GetMessage("MAIN_ADMIN_MENU_EDIT"),
		"ACTION" => $lAdmin->ActionRedirect("bookscatalog_authors_edit.php?&ID=".$a_ID.'&lang='.LANGUAGE_ID),
		"DEFAULT" => true
		);
	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => GetMessage("MAIN_ADMIN_MENU_DELETE"),
		"ACTION" => "if(confirm('".GetMessage('MAIN_AGENT_ALERT_DELETE')."')) ".$lAdmin->ActionDoGroup($a_ID, "delete")
	);

	$row->AddActions($arActions);
}

$lAdmin->AddGroupActionTable(
	array(
		"delete" => true
	)
);
$aContext = array(
	array(
		"TEXT"	=> 'Добавить автора',
		"LINK"	=> "bookscatalog_authors_edit.php?lang=".LANG,
		"TITLE"	=> 'Добавить автора',
		"ICON"	=> "btn_new"
	),
);
$lAdmin->AddAdminContextMenu($aContext);

$APPLICATION->SetTitle('Авторы');
$lAdmin->CheckListMode();

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<input type="hidden" name="lang" value="<?echo LANG?>">
<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		'ID',
		'Компания',
		'Название компании',
		'ИНН компании',
		'Номер контрагента',
		'Документ',
		'Год'
	)
);

$oFilter->Begin();
?>
<tr>
	<td>ID:</td>
	<td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
</tr>
<tr>
	<td>Имя:</td>
	<td><input type="text" name="find_name" size="47" value="<?echo htmlspecialcharsbx($find_name)?>"></td>
</tr>
<tr>
	<td>Фамилия:</td>
	<td><input type="text" name="find_last_name" size="47" value="<?echo htmlspecialcharsbx($find_last_name)?>"></td>
</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>
<?

$lAdmin->DisplayList();

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
