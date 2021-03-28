<?php

\Bitrix\Main\Page\Asset::getInstance()->addCss('/local/components/innopolis/ipr.detail/templates/.default/style.css'); // костыль для слайдера внутри слайдера

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$gridOptions = new \Bitrix\Main\Grid\Options($arResult['GRID_ID']);

$columnIds = $gridOptions->getCurrentOptions()['columns'];
if (strlen($columnIds)) {
    $columnIds = explode(',', $columnIds);
}
$columns = [];
foreach ($arResult['COLUMNS'] as $arColumn) {
    if (is_array($columnIds) && !in_array($arColumn['id'], $columnIds)) {
        continue;
    }
    if (!is_array($columnIds) && !$arColumn['default']) {
        continue;
    }
    $columns[] = $arColumn;
}
$APPLICATION->RestartBuffer();
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=crm_".date('Y-m-d').".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
?>
<table border="1">
    <tr>
        <? foreach ($columns as $arColumn): ?>
            <th><?= $arColumn['name'] ?></th>
        <? endforeach; ?>
    </tr>
    <? foreach ($arResult['RECORDS'] as $arRecord): ?>
        <tr>
            <? foreach ($columns as $arColumn): ?>
                <td><?= $arRecord['data'][$arColumn['id']] ?></td>
            <? endforeach; ?>
        </tr>
    <? endforeach; ?>
</table>
<?php

die;