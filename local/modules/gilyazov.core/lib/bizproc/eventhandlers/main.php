<?php

namespace Gilyazov\Core\Bizproc\EventHandlers;

class Main
{
    public static function OnProlog(){
        \Bitrix\Main\UI\Extension::load('gilyazov.lib');

        global $APPLICATION, $USER;
        $engine = new \CComponentEngine();
        $page = $engine->guessComponentPath(
            '/',
            [
                'proc-list' => 'bizproc/processes/#iblock_id#/view/0/',
                'proc-detail' => 'bizproc/processes/#iblock_id#/element/0/#element_id#/',
                'task-detail' => 'company/personal/user/#user_id#/tasks/task/view/#task_id#/',
                'task-edit' => 'company/personal/user/#user_id#/tasks/task/edit/0/'
            ],
            $variables
        );

        // todo придумать красивый способ
        if ($variables['iblock_id'] == 33) {
            ob_start();
            ?>
            <div class="pagetitle-container">
                <a href="/bitrix/tools/disk/focus.php?folderId=105&action=openFolderList&ncc=1" class="ui-btn ui-btn-light-border ui-btn-icon-download" target="_blank">Скачать шаблон СЗ</a>
            </div>
            <?
            $customHtml = ob_get_clean();
            $GLOBALS['APPLICATION']->AddViewContent('pagetitle', $customHtml, 100);
        }

        /*инструкции*/
        if ($page === 'proc-list'){
            $res = \CIBlock::GetByID($variables['iblock_id']);
            if($arIblock = $res->GetNext()) {
                ob_start();
                echo '<div class="pagetitle"><a href="https://bitrix.innocityhall.ru/knowledge/instructions/'.$arIblock["CODE"].'/" class="ui-btn-main">Инструкция</a></div>';
                $GLOBALS['APPLICATION']->AddViewContent('inside_pagetitle', ob_get_clean(), 500);
            }
        }
        /*end инструкции*/

        if (($page === 'proc-detail') && ($variables["element_id"] != 0)){
            ob_start();

            $documentType = array('lists', 'BizprocDocument', 'iblock_'.$variables['iblock_id']);
            $documentId = array('lists', 'BizprocDocument', $variables['element_id']);
            $arDocumentStates = current(\CBPDocument::GetDocumentStates($documentType, $documentId));
            if ($arDocumentStates){
                $APPLICATION->IncludeComponent("gilyazov:workflow.reviewers",
                    "",
                    array(
                        "ELEMENT_ID" => $variables['element_id'],
                        "IBLOCK_ID" => $variables['iblock_id']
                    ),
                    false
                );

                $APPLICATION->IncludeComponent("bitrix:forum.comments",
                    "",
                    array(
                        "FORUM_ID" => \CBPHelper::getForumId(),
                        "ENTITY_TYPE" => "WF",
                        "ENTITY_ID" => \CBPStateService::getWorkflowIntegerId($arDocumentStates["ID"]),
                        "ENTITY_XML_ID" => "WF_" . $arDocumentStates["ID"],
                        "PERMISSION" => $USER->IsAdmin() ? "X" : "M",
                        "ALLOW_EDIT_OWN_MESSAGE" => "N",
                        "URL_TEMPLATES_PROFILE_VIEW" => "/company/personal/user/#user_id#/",
                        "SHOW_RATING" => "N",
                        "SHOW_LINK_TO_MESSAGE" => "N",
                        "MESSAGES_PER_PAGE" => 200,
                        "BIND_VIEWER" => "Y",
                        "VISIBLE_RECORDS_COUNT" => 200
                    ),
                    false,
                    array('HIDE_ICONS' => 'Y')
                );
            }

            $GLOBALS['APPLICATION']->AddViewContent('sidebar', ob_get_clean(), 100);

            /*команды БП*/
            $APPLICATION->IncludeComponent("gilyazov:document.state",
                "",
                array(
                    "ELEMENT_ID" => $variables['element_id'],
                    "IBLOCK_ID" => $variables['iblock_id']
                ),
                false
            );
            $GLOBALS['APPLICATION']->AddViewContent('topblock', ob_get_clean(), 100);
            /*end команды БП*/
        }
    }
}
