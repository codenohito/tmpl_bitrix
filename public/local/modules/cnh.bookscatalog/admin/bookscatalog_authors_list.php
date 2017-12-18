<?php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_admin.php
// https://dev.1c-bitrix.ru/api_help/main/general/admin.section/rubric_admin_ex.php

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
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

$sTableID = "tbl_".AuthorTable::getTableName();
$oSort = new CAdminSorting($sTableID, "ID", "desc"); // объект сортировки
$lAdmin = new CAdminList($sTableID, $oSort); // основной объект списка

// ******************************************************************** //
//                           Фильтр                                     //
// ******************************************************************** //
function CheckFilter($arrFilterForCheck)
{
	global $lAdmin;
	foreach ($arrFilterForCheck as $f) global $$f;
	// Проверяем значения переменных $find_ИМЯ и, в случае возникновения
	// ошибки, вызываем $lAdmin->AddFilterError("текст_ошибки").
	return count($lAdmin->arFilterErrors) == 0; // если ошибки есть, то false
}

// опишем элементы фильтра и инициализируем его
$FilterArr = Array(
	"find_id",
	"find_name"
);
$lAdmin->InitFilter($FilterArr);

if (CheckFilter($FilterArr))
{
	$arFilter = Array(); // массив фильтрации для выборки GetList()
	if (!empty($find_id)) $arFilter['ID'] = $find_id;
	if (!empty($find_name)) $arFilter['%NAME'] = $find_name;
}

// ******************************************************************** //
//                Обработка действий над элементами списка              //
// ******************************************************************** //

// сохранение отредактированных элементов
/* TODO отработать, протестировать
if($lAdmin->EditAction() && $POST_RIGHT=="W")
{
  // пройдем по списку переданных элементов
  foreach($FIELDS as $ID=>$arFields)
  {
	if(!$lAdmin->IsUpdated($ID))
	  continue;

	// сохраним изменения каждого элемента
	$DB->StartTransaction();
	$ID = IntVal($ID);
	$cData = new AuthorTable;
	if(($rsData = $cData->getByID($ID)) && ($arData = $rsData->fetch()))
	{
	  foreach($arFields as $key=>$value)
		$arData[$key]=$value;
	  if(!$cData->Update($ID, $arData))
	  {
		$lAdmin->AddGroupError("Ошибка сохранения: ".$cData->LAST_ERROR, $ID);
		$DB->Rollback();
	  }
	}
	else
	{
	  $lAdmin->AddGroupError("Ошибка сохранения: нет автора с id", $ID);
	  $DB->Rollback();
	}
	$DB->Commit();
  }
}

// OR ----------->

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
			$lAdmin->AddUpdateError(Loc::getMessage("SAVE_ERROR").$ID.": ".$e->GetString(), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}
*/

// обработка одиночных и групповых действий
if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
	// если выбрано "Для всех элементов" выберем все элементы (с учетом фильтра)
	if($_REQUEST['action_target']=='selected')
	{
	$cData = new AuthorTable;
	$rsData = AuthorTable::getList(array(
		"select" => array('ID'),
		"filter" => $arFilter,
		"order" => array($by => $order)
	));
	while($arRes = $rsData->fetch())
		$arID[] = $arRes['ID'];
	}

	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;
		$ID = IntVal($ID);

		// для каждого элемента совершим требуемое действие
		switch($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!AuthorTable::delete($ID))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError('Ошибка удаления', $ID);
				}
				$DB->Commit();
				break;
		}
	}
}
// ******************************************************************** //
//                Выборка элементов списка                              //
// ******************************************************************** //

// выберем список рассылок
$rsData = AuthorTable::getList(array(
	"filter" => $arFilter,
	"order" => array($by => $order)
));

// echo "<p><pre>"; print_r($rsData); echo "</pre></p>";

// преобразуем список в экземпляр класса CAdminResult
$rsData = new CAdminResult($rsData, $sTableID);

// аналогично CDBResult инициализируем постраничную навигацию.
$rsData->NavStart();

// отправим вывод переключателя страниц в основной объект $lAdmin
$lAdmin->NavText($rsData->GetNavPrint('Записи'));

// ******************************************************************** //
//                Подготовка списка к выводу                            //
// ******************************************************************** //

$lAdmin->AddHeaders(array(
	array(
		"id"      => "ID",
		"content" => "ID",
		"sort"    => "id",
		"align"   => "right",
		"default" => true
	),
	array(
		"id"      => "NAME",
		"content" => "Имя",
		"sort"    =>"name",
		"default" => true
	),
	array(
		"id"      => "LAST_NAME",
		"content" => "Фамилия",
		"sort"    => "last_name",
		"default" => true
	)
));

while($arRes = $rsData->NavNext(true, "f_"))
{
	// создаем строку. результат - экземпляр класса CAdminListRow
	$row =& $lAdmin->AddRow($f_ID, $arRes);

	// далее настроим отображение значений при просмотре и редактировании списка

    // $row->AddField("ID", $f_ID); // поле ставится автоматически

	// параметр NAME будет редактироваться как текст, а отображаться ссылкой
	// $row->AddInputField("NAME", array("size"=>20));
	$row->AddViewField("NAME", '<a href="'.$arAdminUrls['edit'].'?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>');

	// $row->AddInputField("LAST_NAME", array("size"=>20));
	$row->AddField("LAST_NAME", $f_LAST_NAME);

	// $row->AddEditField("LID", CLang::SelectBox("LID", $f_LID));
	// $row->AddInputField("SORT", array("size"=>20));
	// $row->AddCheckField("ACTIVE");
	// $row->AddCheckField("VISIBLE");
	// $row->AddViewField("AUTO", $f_AUTO=="Y"?Loc::getMessage("POST_U_YES"):Loc::getMessage("POST_U_NO"));
	// $row->AddEditField("AUTO", "<b>".($f_AUTO=="Y"?Loc::getMessage("POST_U_YES"):Loc::getMessage("POST_U_NO"))."</b>");

	// сформируем контекстное меню
	$arActions = Array();

	// редактирование элемента
	$arActions[] = array(
		"ICON"    => "edit",
		"DEFAULT" => true,
		"TEXT"    => 'Редактировать',
		"ACTION"  => $lAdmin->ActionRedirect($arAdminUrls['edit']."?ID=".$f_ID)
	);

	$arActions[] = array("SEPARATOR"=>true);

	// удаление элемента
	if ($POST_RIGHT>="W")
		$arActions[] = array(
			"ICON"   => "delete",
			"TEXT"   => 'Удалить',
			"ACTION" => "if(confirm('Будет удалена вся информация, связанная с этой записью')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);

	// если последний элемент - разделитель, почистим мусор.
	if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
		unset($arActions[count($arActions)-1]);

	// применим контекстное меню к строке
	$row->AddActions($arActions);
}

// Почему-то ни на что не влияет
// // резюме таблицы
// $lAdmin->AddFooter(
// 	array(
// 		array( // кол-во элементов
// 			"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
// 			"value" => $rsData->SelectedRowsCount()
// 		),
// 		array( // счетчик выбранных элементов
// 			"counter" => true,
// 			"title"   => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
// 			"value"   => "0"
// 		)
// 	)
// );

// групповые действия
$lAdmin->AddGroupActionTable(array(
	"delete" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE")
));

// ******************************************************************** //
//                Административное меню                                 //
// ******************************************************************** //

// сформируем меню из одного пункта - добавление элемента
$aContext = array(
	array(
		"TEXT"  => 'Добавить автора',
		"LINK"  => $arAdminUrls['edit']."?lang=".LANG,
		"TITLE" => 'Добавить автора',
		"ICON"  => "btn_new"
	)
);

// и прикрепим его к списку
$lAdmin->AddAdminContextMenu($aContext);

// ******************************************************************** //
//                Вывод                                                 //
// ******************************************************************** //

$lAdmin->CheckListMode(); // альтернативный вывод
$APPLICATION->SetTitle("Авторы");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// ******************************************************************** //
//                Вывод фильтра                                         //
// ******************************************************************** //

// создадим объект фильтра
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		"ID",
		"Имя",
	)
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>
<!-- <tr>
	<td><b><?//=Loc::getMessage("rub_f_find")?>:</b></td>
	<td>
		<input type="text" size="25" name="find" value="<?// echo htmlspecialchars($find)?>" title="<?//=Loc::getMessage("rub_f_find_title")?>">
		<?/*
		$arr = array(
			"reference" => array(
				"ID",
			),
			"reference_id" => array(
				"id",
			)
		);
		echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
		*/
		?>
	</td>
</tr> -->
<tr>
	<td><?="ID"?>:</td>
	<td>
		<input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>">
	</td>
</tr>
<tr>
	<td><?="Имя:"?></td>
	<td><input type="text" name="find_name" size="60" value="<?echo htmlspecialchars($find_name)?>"></td>
</tr>
<?
$oFilter->Buttons(array(
	"table_id" => $sTableID,
	"url"      => $APPLICATION->GetCurPage(),
	"form"     => "find_form"
));
$oFilter->End();
?>
</form>

<?
$lAdmin->DisplayList(); // выведем таблицу списка элементов

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
