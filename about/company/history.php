<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/about/company/history.php");

$APPLICATION->SetTitle(GetMessage("ABOUT_TITLE"));
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>