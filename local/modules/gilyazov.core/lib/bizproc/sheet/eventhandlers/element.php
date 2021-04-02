<?php


namespace Gilyazov\Core\Bizproc\Sheet\EventHandlers;


class Element
{

    public function OnAfterIBlockElementAddHandler(&$arFields)
    {
        $res = \CIBlock::GetByID($arFields["IBLOCK_ID"]);
        if($arIblock = $res->GetNext()) {
            $iblockType = $arIblock['IBLOCK_TYPE_ID'];
        }

        if ($iblockType === 'bitrix_processes') {
            $elementID = $arFields['ID'];
            $documentType = array('lists', 'BizprocDocument', 'iblock_'.$arFields["IBLOCK_ID"]);

            $res = \CBPWorkflowTemplateLoader::GetList(['ID' => 'DESC'], ['DOCUMENT_TYPE' => $documentType], $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array());
            if($arFields = $res->GetNext())
            {
                foreach ($arFields['TEMPLATE'][0]['Children'] as $arTemplate){
                    $statusName = $arTemplate['Properties']['Title'];
                    // команды статусов
                    $activityList = self::searchApproveActivity($arTemplate, true);

                    $sheet[] = [
                        'id' => $arTemplate['Name'],
                        'name' => $statusName,
                        'approveActivity' => $activityList
                    ];
                }

                // сохраним как json в элемент
                \CIBlockElement::SetPropertyValuesEx($elementID, false, array("REVIEWERS_JSON" => \Bitrix\Main\Web\Json::encode($sheet)));
            }
        }
    }

    protected static function searchApproveActivity($arTasks, $first = false): array
    {
        static $result = [];

        if ($first){
            $result = [];
        }
        if ($arTasks['Type'] === 'ApproveActivity') {
            $result[] = $arTasks['Properties'];
        }

        foreach ($arTasks['Children'] as $arTask) {
            if (is_array($arTask['Children'])) {
                self::searchApproveActivity($arTask);
            }
        }

        return $result;
    }
}