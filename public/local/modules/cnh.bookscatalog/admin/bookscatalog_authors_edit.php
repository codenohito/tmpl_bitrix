<?php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_edit.php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_edit_ex.php

use Bitrix\Main\Localization\Loc;
use Cnh\BooksCatalog\AuthorTable;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'cnh.bookscatalog');

$arAdminUrls = array(
	'list' => 'bookscatalog_authors_list.php',
	'edit' => 'bookscatalog_authors_edit.php'
);

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

Loc::loadMessages(__FILE__);

// проверяем, подключен ли наш модуль и есть ли на него права у текущего пользователя
$POST_RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if ((!CModule::IncludeModule(ADMIN_MODULE_NAME)) || ($POST_RIGHT == "D"))
{
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

// Добавляем закладки страницы
// Делаем это до всех остальных данных, так как нам пригодится этот объект
// для формирования верной ссылки редиректа после сохранения
$aTabs = array(
	array("DIV" => "edit1", "TAB" => "Автор", "ICON"=>"main_user_edit", "TITLE"=>"Автор")
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

// Объявляем несколько важных переменных
$ID = intval($ID);      // идентификатор редактируемой записи // intval($_REQUEST['ID'])
$errMessages = null;    // сообщения об ошибке
$bVarsFromForm = false; // флаг "Данные получены с формы"

// ************************************************************************** //
//         Обработка и сохранение изменений                                   //
// ************************************************************************** //

// Удаление записи
if(($ID > 0)
		&& $action == 'delete' // $_REQUEST['action']
		&& $POST_RIGHT == "W"
		&& check_bitrix_sessid()
)
{
	AuthorTable::delete($ID);
	LocalRedirect($arAdminUrls['list']."?lang=".LANG);
}

// Сохранение или обновление записи
if($REQUEST_METHOD == "POST"
		&& ($save != "" || $apply != "") // $_REQUEST['save']
		&& $POST_RIGHT == "W"
		&& check_bitrix_sessid()
)
{
	// обработка данных формы
	$arFields = Array(
		'NAME'      => $NAME,     // $_POST['NAME'],
		'LAST_NAME' => $LAST_NAME // $_POST['LAST_NAME'],
	);

	// сохранение данных
	if($ID > 0)
	{
		$result = AuthorTable::update($ID, $arFields);
	}
	else
	{
		$result = AuthorTable::add($arFields);
		$ID = $result->getId();
	}

	if($result->isSuccess())
	{
		if ($apply != "")
			LocalRedirect(
				"/bitrix/admin/".$arAdminUrls['edit']."?ID=".$ID
					."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
		else
			LocalRedirect("/bitrix/admin/".$arAdminUrls['list']."?lang=".LANG);
	}
	else
	{
		$errMessages = $result->getErrorMessages();
		$bVarsFromForm = true;
	}
}

// ************************************************************************** //
//         Выборка и подготовка данных для формы                              //
// ************************************************************************** //

// значения по умолчанию
$str_NAME      = '';
$str_LAST_NAME = '';

if($ID > 0)
{
	$author = AuthorTable::getRowByID($ID);
	if($author)
	{
		$str_NAME = $author['NAME'];
		$str_LAST_NAME = $author['LAST_NAME'];
	}
	else
		$ID = 0;
}

// если данные переданы из формы, инициализируем их
if($bVarsFromForm)
	$DB->InitTableVarsForEdit(AuthorTable::getTableName(), "", "str_");

// ************************************************************************** //
//         Вывод данных                                                       //
// ************************************************************************** //

// установим заголовок страницы
$APPLICATION->SetTitle($ID > 0 ? 'Редактирование автора' : 'Добавление нового автора');

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

// Задание параметров административного меню
$aMenu = array(
	array(
		"TEXT"  => 'Вернуться в список',
		"TITLE" => 'Вернуться в список',
		"LINK"  => $arAdminUrls['list']."?lang=".LANG,
		"ICON"  => "btn_list",
	)
);
if($ID > 0)
{
	$aMenu[] = array("SEPARATOR"=>"Y");

	$aMenu[] = array(
		"TEXT"  => 'Добавить автора',
		"TITLE" => 'Добавить автора',
		"LINK"  => $arAdminUrls['edit']."?lang=".LANG,
		"ICON"  => "btn_new",
	);
	$aMenu[] = array(
		"TEXT"  => 'Удалить автора',
		"TITLE" => 'Удалить автора',
		"LINK"  => "javascript:if(confirm('Будет удалена вся информация, связанная с этим автором. Продолжить?'))".
			"window.location='".$arAdminUrls['edit']."?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
		"ICON"  => "btn_delete",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show(); // выведем меню

// Если есть сообщения об ошибках или об успешном сохранении - выведем их.
if($_REQUEST["mess"] == "ok" && $ID > 0)
	CAdminMessage::ShowMessage(array("MESSAGE"=>'Автор сохранён', "TYPE"=>"OK"));
if (!empty($errMessages))
	CAdminMessage::ShowMessage(join("\n", $errMessages));


// ************************************************************************** //
//         Форма                                                              //
// ************************************************************************** //
?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" name="post_form">
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?if($ID > 0 && !$bCopy):?>
	<input type="hidden" name="ID" value="<?=$ID?>">
<?endif;?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr id="tr_NAME">
		<td width="40%">
			<span class="required">*</span>
			Имя:
		</td>
		<td width="60%">
			<input type="text" name="NAME" value="<?=$str_NAME?>" size="50" maxlength="255">
		</td>
	</tr>

	<tr id="tr_LAST_NAME">
		<td width="40%">
			<span class="required">*</span>
			Фамилия:
		</td>
		<td width="60%">
			<input type="text" name="LAST_NAME" value="<?=$str_LAST_NAME?>" size="50" maxlength="255">
		</td>
	</tr>
<?
$tabControl->Buttons(
	array(
		"disabled" => ($POST_RIGHT < "W"),
		"back_url" => $arAdminUrls['list']."?lang=".LANG,
	)
);
// завершаем интерфейс закладки
$tabControl->End();
// $tabControl->ShowWarnings("post_form", $errAdminMessage);
?>
</form>

<?
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
