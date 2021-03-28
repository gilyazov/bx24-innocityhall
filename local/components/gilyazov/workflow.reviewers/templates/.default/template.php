<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->addExternalCss(SITE_TEMPLATE_PATH."/css/sidebar.css");
/**/
?>
<div class="sidebar-widget sidebar-widget-bp">
    <div class="sidebar-widget-top">
        <div class="sidebar-widget-top-title">Лист согласования</div>
    </div>
    <?foreach ($arResult['SHEET'] as $arStatus):?>
        <span class="task-item">
            <?=$arStatus['name']?>
        </span>

        <?foreach ($arStatus['approveActivity'] as $arActivity):?>
            <?foreach ($arActivity['REAL_USER'] as $userID => $arUser):?>
                <a href="/company/personal/user/<?=$arUser["ID"]?>/" class="sidebar-widget-item<?if(++$i == count($arActivity['REAL_USER'])):?> widget-last-item<?endif?>">
                    <span class="user-avatar user-default-avatar"
                        <?if (isset($arUser["PERSONAL_PHOTO"])):?>
                            style="background: url('<?=CFile::GetPath($arUser["PERSONAL_PHOTO"])?>') no-repeat center; background-size: cover;"
                        <?endif?>>
                    </span>
                    <span class="sidebar-user-info">
                        <span class="user-birth-name"><?=$arUser['LAST_NAME']?> <?=$arUser['NAME']?></span>
                        <span class="user-birth-date"><?=$arUser["WORK_POSITION"]?></span>
                    </span>
                </a>
            <?endforeach;?>
        <?endforeach;?>
    <?endforeach;?>
</div>