<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заявки");
?><?$APPLICATION->IncludeComponent(
	"bitrix:lists",
	"",
	Array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE_ID" => "bizproc",
		"SEF_MODE" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"SEF_FOLDER" => "/processes/",
		"SEF_URL_TEMPLATES" => Array("lists"=>"","list"=>"#list_id#/view/#section_id#/","list_sections"=>"#list_id#/edit/#section_id#/","list_edit"=>"#list_id#/edit/","list_fields"=>"#list_id#/fields/","list_field_edit"=>"#list_id#/field/#field_id#/","list_element_edit"=>"#list_id#/element/#section_id#/#element_id#/","list_file"=>"#list_id#/file/#section_id#/#element_id#/#field_id#/#file_id#/","bizproc_log"=>"#list_id#/bp_log/#document_state_id#/","bizproc_workflow_start"=>"#list_id#/bp_start/#element_id#/","bizproc_workflow_delete"=>"#list_id#/bp_delete/#element_id#/","bizproc_task"=>"#list_id#/bp_task/#section_id#/#element_id#/#task_id#/","bizproc_workflow_admin"=>"#list_id#/bp_list/","bizproc_workflow_edit"=>"#list_id#/bp_edit/#ID#/","bizproc_workflow_vars"=>"#list_id#/bp_vars/#ID#/","bizproc_workflow_constants"=>"#list_id#/bp_constants/#ID#/","list_export_excel"=>"#list_id#/excel/","catalog_processes"=>"catalog_processes/"),
		"VARIABLE_ALIASES" => Array("lists"=>Array(),"list"=>Array(),"list_sections"=>Array(),"list_edit"=>Array(),"list_fields"=>Array(),"list_field_edit"=>Array(),"list_element_edit"=>Array(),"list_file"=>Array(),"bizproc_log"=>Array(),"bizproc_workflow_start"=>Array(),"bizproc_workflow_delete"=>Array(),"bizproc_task"=>Array(),"bizproc_workflow_admin"=>Array(),"bizproc_workflow_edit"=>Array(),"bizproc_workflow_vars"=>Array(),"bizproc_workflow_constants"=>Array(),"list_export_excel"=>Array(),"catalog_processes"=>Array(),),
		"VARIABLE_ALIASES" => Array(
			"lists" => Array(),
			"list" => Array(),
			"list_sections" => Array(),
			"list_edit" => Array(),
			"list_fields" => Array(),
			"list_field_edit" => Array(),
			"list_element_edit" => Array(),
			"list_file" => Array(),
			"bizproc_log" => Array(),
			"bizproc_workflow_start" => Array(),
			"bizproc_workflow_delete" => Array(),
			"bizproc_task" => Array(),
			"bizproc_workflow_admin" => Array(),
			"bizproc_workflow_edit" => Array(),
			"bizproc_workflow_vars" => Array(),
			"bizproc_workflow_constants" => Array(),
			"list_export_excel" => Array(),
			"catalog_processes" => Array(),
		)
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>