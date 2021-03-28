<?php
require_once('event_handlers.php');

CJSCore::RegisterExt('PROCESSES_ELEMENT', array(
    'js' => array('/local/modules/gilyazov.core/lib/js/processes_element.js'),
    'rel' => array()
));
CUtil::InitJSCore(array('PROCESSES_ELEMENT'));