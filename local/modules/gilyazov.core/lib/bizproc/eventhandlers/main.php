<?php

namespace Gilyazov\Core\Bizproc\EventHandlers;

class Main
{
    public static function OnProlog(){
        global $APPLICATION, $USER;
        $engine = new \CComponentEngine();
        $assetManager = \Bitrix\Main\Page\Asset::getInstance();
        $assetManager->addCss("/local/js/gilyazov/template/style.css");

        $page = $engine->guessComponentPath(
            '/',
            [
                'proc-detail' => 'bizproc/processes/#iblock_id#/element/0/#element_id#/',
                'task-detail' => 'company/personal/user/#user_id#/tasks/task/view/#task_id#/',
                'task-edit' => 'company/personal/user/#user_id#/tasks/task/edit/0/'
            ],
            $variables
        );

        // todo придумать красивый способ
        if ($variables['proc_id'] == 33) {
            ob_start();
            ?>
            <div class="pagetitle-container">
                <a href="/bitrix/tools/disk/focus.php?folderId=105&action=openFolderList&ncc=1" class="ui-btn ui-btn-light-border ui-btn-icon-download" target="_blank">Скачать шаблон СЗ</a>
            </div>
            <?
            $customHtml = ob_get_clean();
            $GLOBALS['APPLICATION']->AddViewContent('pagetitle', $customHtml, 100);
        }

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
            $customHtml = ob_get_clean();
            $GLOBALS['APPLICATION']->AddViewContent('sidebar', $customHtml, 100);
        }

        /*if ($page === 'task-detail'){
            \Bitrix\Main\UI\Extension::load('gilyazov.lib');
        }
        if ($page === 'task-edit'){
            $assetManager->addJs('/local/js/gilyazov/lib/task/task_edit.js');
        }*/
    }
}
