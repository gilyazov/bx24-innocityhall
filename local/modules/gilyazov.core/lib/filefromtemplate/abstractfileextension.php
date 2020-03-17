<?php

namespace Gilyazov\Core\FileFromTemplate;

use \CFile, \LogicException, \Bitrix\Main\SystemException, \ReflectionClass;

abstract class AbstractFileExtension
{
    /** @var string Путь к сгенерированному файлу */
    protected $generatedFilePath;

    /** @var string Путь к файлу шаблона */
    protected $templateFilePath;

    /** @var array Поля подставляемые в шаблон файла */
    protected $fields;

    /** @var array Параметры генерации файла, каждый параметр задаётся константой */
    protected $params;

    /** @var bool Флаг, если true файл не будет удалён в деструкторе */
    protected $generatedFileBeenSaved = false;

    /**
     * Получение расширения файлов класса (def. имя класса в нижнем регистре)
     *
     * @return string
     */
    protected static function getFileExtension(): string
    {
        // return strtolower(get_class_name(static::class));
        return strtolower('Docx');
    }

    /**
     * Получение пути к дирректории в которой лежит шаблоны данного расширения файлов
     *
     * @return string
     */
    protected static function getTemplateDirectoryPath(): string
    {
        return $_SERVER['DOCUMENT_ROOT'].'/local/file_templates/'.static::getFileExtension();
    }

    /**
     * AbstractFileExtension constructor.
     *
     * @param string $templateName
     * @param array $fields
     * @param array $params
     */
    public function __construct(string $templateName, array $fields, array $params = [])
    {
        $this->templateFilePath = static::getTemplateDirectoryPath().DIRECTORY_SEPARATOR.$templateName.'.'.static::getFileExtension();
        $this->fields = $fields;
        $this->params = $params;
        $this->createGeneratedFile();
    }

    /**
     * Деструктор удаляет сгенерированный(только временный) файл
     */
    public function __destruct()
    {
        if (!$this->generatedFileBeenSaved && file_exists($this->generatedFilePath)) {
            unlink($this->generatedFilePath);
        }
    }

    /**
     * Создание генерируемого документа путём копирования шаблона
     */
    protected function createGeneratedFile()
    {
        $this->generatedFilePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.randString(12).'.'.static::getFileExtension();
        copy($this->templateFilePath, $this->generatedFilePath);
    }

    /**
     * Сохраняет сгенерированный файл в БД для последущего использования
     *
     * @return int
     * @throws SystemException
     */
    public function saveGeneratedFile(): int
    {
        if ($this->generatedFileBeenSaved) {
            throw new LogicException('File has already been saved');
        }
        $generatedFilePath = $this->getGeneratedFilePath();
        $fileId = CFile::SaveFile(CFile::MakeFileArray($generatedFilePath), 'file_from_template/'.self::getFileExtension());
        if (!$fileId) {
            throw new SystemException('Temporary file was not saved. File Id empty or null');
        }
        $permanentFilePath = $_SERVER['DOCUMENT_ROOT'].CFile::GetPath($fileId);
        if (!$permanentFilePath) {
            throw new SystemException('Temporary file was not saved. Permanent file path empty or null');
        }
        if (!file_exists($permanentFilePath)) {
            throw new SystemException('Temporary file was not saved. Permanent file been declared, but not exist');
        }
        unlink($generatedFilePath);
        $this->generatedFileBeenSaved = true;
        $this->generatedFilePath = $permanentFilePath;

        return $fileId;
    }

    /**
     * Подстановка в файл входных данных
     *
     * @return mixed
     */
    abstract public function generate();

    /**
     * Получение пути к сгенерированному файлу
     *
     * @return string
     * @throws SystemException
     */
    public function getGeneratedFilePath(): string
    {
        if (!$this->generatedFilePath) {
            throw new LogicException('Tmp file not exist: file was not generated');
        }
        if (!file_exists($this->generatedFilePath)) {
            throw new SystemException('Tmp file been declared, but not exist');
        }

        return $this->generatedFilePath;
    }

    /**
     * Получение сгенерированного файла строкой
     *
     * @return string
     * @throws SystemException
     */
    public function getGeneratedFileAsString(): string
    {
        return (string)file_get_contents($this->getGeneratedFilePath());
    }
}