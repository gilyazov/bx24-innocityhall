<?php

namespace Gilyazov\Core\Bizproc\Sheet\EventHandlers;

class Task
{

    /*
     * Вызывается в момент согласования
     * */
    public static function OnTaskMarkCompleted($taskId, $userId, $status){
        $arTask = self::getTaskInfo($taskId);
        $elementId = $arTask['PARAMETERS']['DOCUMENT_ID'][2];
        $arSheet = self::getSheetArr($elementId);
        $arNewSheet = self::modifySheet($arSheet, $arTask, $status, $userId);
        self::updateSheet($elementId, $arNewSheet);

        // \AddMessage2Log();
    }

    protected static function getTaskInfo(int $taskId)
    {
        $res = \CBPTaskService::GetList(['ID' => 'DESC'], ['ID' => $taskId], $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array());
        if($arTask = $res->GetNext())
        {
            return $arTask;
        }
    }

    protected static function modifySheet($arSheet, $arTask, $status, $userId)
    {
        foreach ($arSheet as &$step)
        {
            foreach ($step['approveActivity'] as $name => &$activity)
            {
                if($name == $arTask['ACTIVITY_NAME'])
                {
                    switch ($status) {
                        case 1:
                            $activity['REVIEWED'][$userId] = [
                                'ID' => $userId,
                                'MODIFIED' => $arTask['MODIFIED'],
                            ];
                            break;
                        case 2:
                            $activity['REPLACED'][$userId] = [
                                'ID' => $userId,
                                'MODIFIED' => $arTask['MODIFIED'],
                            ];
                            break;
                    }
                }
            }
        }

        return $arSheet;
    }

    protected static function getSheetArr(int $elementId)
    {
        \CModule::IncludeModule('iblock');
        $arSelect = Array("ID", "PROPERTY_REVIEWERS_JSON");
        $arFilter = Array("ID" => $elementId, "!PROPERTY_REVIEWERS_JSON" => false);
        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();

            return \Bitrix\Main\Web\Json::decode($arFields['~PROPERTY_REVIEWERS_JSON_VALUE']);
        }
    }

    protected static function updateSheet(int $elementId, $sheet)
    {
        \CIBlockElement::SetPropertyValuesEx($elementId, false, array("REVIEWERS_JSON" => \Bitrix\Main\Web\Json::encode($sheet)));
    }
}
