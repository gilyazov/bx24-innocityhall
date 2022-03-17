# Необходимые правки ядра
Добавление комментария согласования в обработчик событий.

```
/bitrix/activities/bitrix/approveactivity/approveactivity.php
377:$taskService->MarkCompleted(....
/bitrix/modules/bizproc/classes/general/taskservice.php
50:ExecuteModuleEventEx($arEvent, array($taskId, $userId, $status, $comment));
```
