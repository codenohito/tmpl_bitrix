<?php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_edit.php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_edit_ex.php

use Bitrix\Main\Localization\Loc;
use Cnh\BooksCatalog\AuthorTable;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'cnh.bookscatalog');

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

Loc::loadMessages(__FILE__);

if (!CModule::IncludeModule(ADMIN_MODULE_NAME))
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$is_create_form = true;
$is_update_form = false;

$isEditMode = true;

$errors = array();

$entity = AuthorTable::getEntity();
$entity_data_class = $entity->getDataClass();
$entity_table_name = AuthorTable::getTableName();

// get row
$row = null;

if (isset($_REQUEST['ID']) && $_REQUEST['ID'] > 0)
{
	$row = $entity_data_class::getById($_REQUEST['ID'])->fetch();

	if (!empty($row))
	{
		$is_update_form = true;
		$is_create_form = false;
	}
	else
	{
		$row = null;
	}
}

if ($is_create_form)
{
	$APPLICATION->SetTitle('Создание автора');
}
else
{
	$APPLICATION->SetTitle('Редактирование автора');
}

// form
$aTabs = array(
	array("DIV" => "edit1", "TAB" => 'Автор', "ICON"=>"ad_contract_edit", "TITLE"=> 'Автор')
);

$tabControl = new CAdminForm("hlrow_edit_DocumentTable", $aTabs);

// delete action
if ($is_update_form && isset($_REQUEST['action']) && $_REQUEST['action'] === 'delete' && check_bitrix_sessid())
{

	// if( Access::getInstance()->checkRigth__Document($row['ID']) ) {
		$entity_data_class::delete($row['ID']);
	// }


	LocalRedirect("bookscatalog_authors_list.php?lang=".LANGUAGE_ID);
}

// save action
if ((strlen($save)>0 || strlen($apply)>0) && $REQUEST_METHOD=="POST" && check_bitrix_sessid())
{
	$data = array();
//	$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_DocumentTable', $data);
	$data = array(
		'NAME' => $_POST['NAME'],
		'LAST_NAME' => $_POST['LAST_NAME']
	);

	/** @param Bitrix\Main\Entity\AddResult $result */
	if ($is_update_form)
	{
		$ID = intval($_REQUEST['ID']);
		$result = $entity_data_class::update($ID, $data);
	}
	else
	{
		$result = $entity_data_class::add($data);
		$ID = $result->getId();
	}

	if($result->isSuccess())
	{
		if (strlen($save)>0)
		{
			LocalRedirect("bookscatalog_authors_list.php?lang=".LANGUAGE_ID);
		}
		else
		{
			LocalRedirect("bookscatalog_authors_edit.php?ID=".intval($ID)."&lang=".LANGUAGE_ID."&".$tabControl->ActiveTabParam());
		}
	}
	else
	{
		$errors = $result->getErrorMessages();
	}
}

// menu
$aMenu = array(
	array(
		"TEXT"	=> 'Вернуться в список',
		"TITLE"	=> 'Вернуться в список',
		"LINK"	=> "bookscatalog_authors_list.php?&lang=".LANGUAGE_ID,
		"ICON"	=> "btn_list",
	)
);

$context = new CAdminContextMenu($aMenu);

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

$context->Show();


if (!empty($errors))
{
	CAdminMessage::ShowMessage(join("\n", $errors));
}

$tabControl->BeginPrologContent();

echo $USER_FIELD_MANAGER->ShowScript();

echo CAdminCalendar::ShowScript();

$tabControl->EndPrologContent();
$tabControl->BeginEpilogContent();
?>

<?=bitrix_sessid_post()?>
	<input type="hidden" name="ID" value="<?=htmlspecialcharsbx(!empty($row)?$row['ID']:'')?>">
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">

<?$tabControl->EndEpilogContent();?>

<? $tabControl->Begin(array(
	"FORM_ACTION" => $APPLICATION->GetCurPage()."?ID=".IntVal($ID)."&lang=".LANG
));?>

<? $tabControl->BeginNextFormTab(); ?>

<? $tabControl->AddViewField("ID", "ID", !empty($row)?$row['ID']:''); ?>
<?
// ----------------- ВЫВОДИМ ПОЛЯ ФОРМЫ -----------------
$tabControl->AddEditField("NAME", "Имя", true, array('size' => 50), !empty($row['NAME'])?$row['NAME']:'');
$tabControl->AddEditField("LAST_NAME", "Фамилия", true, array('size' => 50), !empty($row['LAST_NAME'])?$row['LAST_NAME']:'');

if(!empty($row['ACT_HREF']))
{
	$flhjr = '<a href="' . $row['ACT_HREF'] . '" target="_blank">скачать ' . $row['ACT_FILE'] . '</a>';
	$tabControl->AddViewField("ACT_HREF", "Уже загруженный файл", $flhjr);
}

$tabControl->Buttons(array("disabled" => $disable, "back_url"=>"highloadblock_rows_list.php?lang=".LANGUAGE_ID));
$tabControl->Show();
?>
	</form>

<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
