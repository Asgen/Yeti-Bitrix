<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");?><?$APPLICATION->IncludeComponent(
	"bitrix:main.register",
	"yeti-registration",
	Array(
		"AUTH" => "Y",
		"REQUIRED_FIELDS" => array("EMAIL","NAME","WORK_NOTES"),
		"SET_TITLE" => "Y",
		"SHOW_FIELDS" => array("EMAIL","NAME","PERSONAL_PHOTO","WORK_NOTES"),
		"SUCCESS_PAGE" => "/",
		"USER_PROPERTY" => array(),
		"USER_PROPERTY_NAME" => "",
		"USE_BACKURL" => "Y"
	)
);?><? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>