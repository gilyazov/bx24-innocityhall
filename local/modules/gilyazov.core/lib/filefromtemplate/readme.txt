Шаблоны файлов стандартно находятся в /local/file_templates/[расширение]/[имя_шаблона].[расширение]
Прим. сохранения файла:
use Bitrix\Main\Loader;
Loader::includeModule('innopolis.common');
$File = new \Innopolis\Common\FileFromTemplate\Docx('example', ['EMPLOYEE_NAME' => 'MY EXAMPLE TEST'], array('USE_REGEX'=>true));
$File->generate();
$fileId = $File->saveGeneratedFile();
var_dump('File Id: '.$fileId.'File_path: '.$File->getGeneratedFilePath());