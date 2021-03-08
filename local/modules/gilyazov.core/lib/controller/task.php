<?php

namespace Gilyazov\Core\Controller;

use Bitrix\Main\Engine\Controller;

class Task extends Controller
{
    public function infoAction($post)
    {
        if (!\Bitrix\Main\Loader::IncludeModule('tasks')) {
            throw new \Exception("Task module is not installed");
        }

        $task = new \Bitrix\Tasks\Item\Task((int)$post['taskId']);

        return $task->getData();
    }
}