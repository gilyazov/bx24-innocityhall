<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
require_once  $_SERVER["DOCUMENT_ROOT"] . '/bitrix/vendor/autoload.php';
$APPLICATION->RestartBuffer();


$html = '
<html><head>
<style>
table {
	font-family: sans-serif;
	border: .5mm solid #ccc;
	border-collapse: collapse;
}
td {
	padding: 3mm;
	border-bottom: .5mm solid #ccc;
	vertical-align: middle;
}
</style>
</head>
<body>';
$html .= '<table><thead><tr><td>Согласующий</td><td>Решение</td></tr></thead>';
foreach ($arResult['SHEET'] as $arStatus){
    if (!current($arStatus['approveActivity'])['REAL_USER']){
        continue;
    }

    $html .= "<tr><td colspan='2'><b>$arStatus[name]</b></td></tr>";

    foreach ($arStatus['approveActivity'] as $arActivity)
    {
        $html .= '<tr>';
        foreach ($arActivity['REAL_USER'] as $userID => $arUser)
        {
            $html .= "<td>$arUser[LAST_NAME] $arUser[NAME]</td>";

            if($arActivity['REVIEWED'][$arUser["ID"]]){
                $html .= "<td>Согласовано: <br><small>".$arActivity['REVIEWED'][$arUser["ID"]]['MODIFIED']."</small></td>";
            }
            elseif($arActivity['REPLACED'][$arUser["ID"]]){
                $html .= "<td>Отклонено: <br><small>".$arActivity['REPLACED'][$arUser["ID"]]['MODIFIED']."</small></td>";
            }
            else{
                $html .= "<td>Ожидание решения</td>";
            }
        }
        $html .= '</tr>';
    }
}
$html .= '</table></body></html>';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output();

die;