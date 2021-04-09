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

        self::addComment($elementId, $userId, $status);
    }

    protected static function addComment($elementId, $userId, $status)
    {
        \Bitrix\Main\Loader::includeModule('forum');
        \Bitrix\Main\Loader::includeModule('iblock');

        $res = \CIBlockElement::GetByID($elementId);
        if($arElement = $res->GetNext()){
            $newTopic = 'N';
            $documentType = array('lists', 'BizprocDocument', 'iblock_'.$arElement['IBLOCK_ID']);
            $documentId = array('lists', 'BizprocDocument', $arElement['ID']);
            $arDocumentStates = current(\CBPDocument::GetDocumentStates($documentType, $documentId));

            $db_res = \CForumTopic::GetList(array("SORT"=>"ASC"), array("FORUM_ID"=>\CBPHelper::getForumId(), "XML_ID" => "WF_" . $arDocumentStates["ID"]));
            if ($arTopic = $db_res->Fetch())
            {
                $TID = $arTopic['ID'];
            }
            else{
                $arFields = Array(
                    "TITLE" => "TOPIC",
                    "FORUM_ID" => \CBPHelper::getForumId(),
                    "USER_START_ID" => $userId,
                    "USER_START_NAME" => $GLOBALS['USER']->GetFullName(),
                    "LAST_POSTER_NAME" => $GLOBALS['USER']->GetFullName(),
                    "XML_ID" => "WF_" . $arDocumentStates["ID"],
                    "APPROVED" => "Y",
                    "PERMISSION_EXTERNAL" => "M",
                    "PERMISSION" => "M",
                );
                $TID = \CForumTopic::Add($arFields);
                $newTopic = 'Y';
            }

            if ($status == 1){
                $message = '[B]Заявка согласована[/B]';
            }
            else{
                $message = '[B]Заявка отклонена[/B]';
            }
            //$message .= '<br>' . $arTask['PARAMETERS']['CommentLabelMessage'];
            $arFields = Array(
                "POST_MESSAGE" => $message,
                "AUTHOR_ID" => $userId,
                "AUTHOR_NAME" => $GLOBALS['USER']->GetFullName(),
                "FORUM_ID" => \CBPHelper::getForumId(),
                "TOPIC_ID" => $TID,
                "APPROVED" => "Y",
                "NEW_TOPIC" => $newTopic,
                "PARAM2" => intVal(\CBPStateService::getWorkflowIntegerId($arDocumentStates["ID"])),
                "PERMISSION_EXTERNAL" => "M",
                "PERMISSION" 	=> "M",
                "IS_SERVICE_MESSAGE" => "Y"
            );

            $ID = \CForumMessage::Add($arFields);
            if ($ID<=0 && $ex=$GLOBALS['APPLICATION']->GetException())
                \AddMessage2Log($ex->GetString());
        }
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
