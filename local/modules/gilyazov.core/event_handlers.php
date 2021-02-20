<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

// костыль для исправления логики работы пользовательского свойства "Привязка к пользователю"
AddEventHandler("main", "OnBeforeProlog", Array("\\Gilyazov\\Core\\EventHandlers\\Main", "OnBeforeProlog"));
AddEventHandler("main", "OnProlog", Array("\\Gilyazov\\Core\\Bizproc\\EventHandlers\\Main", "OnProlog"));