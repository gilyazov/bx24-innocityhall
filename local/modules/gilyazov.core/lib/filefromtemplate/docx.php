<?php

namespace Gilyazov\Core\FileFromTemplate;

class Docx extends AbstractFileExtension
{
    /** @var string Если true поиск замен происходит также в верхнем колонитуле */
    const PARAMETER_INCLUDE_TOP_COLONTITUL = 'INCLUDE_TOP_COLONTITUL';

    /** @var string Если true поиск замен происходит также в нижнем колонитуле */
    const PARAMETER_INCLUDE_BOTTOM_COLONTITUL = 'INCLUDE_BOTTOM_COLONTITUL';

    /** @var string Если true все переносы строк будут заменены на переносы строк ворда  */
    const PARAMETER_REPLACE_NEWLINE = 'REPLACE_NEWLINE';

    /** @var string Если true все переносы строк будут заменены на переносы строк ворда  */
    const PARAMETER_USE_REGEX = 'INCLUDE_BOTTOM_COLONTITUL';

    public function generate()
    {
        $zip = new \ZipArchive();
        $zip->open($this->getGeneratedFilePath());
        $docFiles = [
            'word/document.xml'
        ];
        if ($this->params[static::PARAMETER_INCLUDE_TOP_COLONTITUL]) {
            $docFiles[] = 'word/header1.xml';
        }
        if ($this->params[static::PARAMETER_INCLUDE_BOTTOM_COLONTITUL]) {
            $docFiles[] = 'word/footer1.xml';
        }
        foreach ($docFiles as $docFile) {
            $templateText = $zip->getFromName($docFile);
            if (false === $templateText) {
                // Файл не найден
                continue;
            }
            $zip->addFromString($docFile, $this->generateText($templateText));
        }
        $zip->close();
    }

    /**
     * Заполнение текста входными данными
     * @param string $templateText полный текст для обработки
     *
     * @return string
     */
    protected function generateText(string $templateText): string
    {
        return $this->replacePlaceholders($templateText);
    }

    /**
     * Заменяем в тексте ключи данных на входные данные
     * @param string $templateText полный текст для обработки
     *
     * @return string
     */
    protected function replacePlaceholders(string $templateText): string {
        if ($this->params['REPLACE_NEWLINE']) {
            $this->fields = $this->replaceNewLines($this->fields);
        }
        $result = $templateText;
        if ($this->params['USE_REGEX']) {
            $replacers = $this->getReplacers($result);
            foreach ($replacers as $value) {
                // Очищаем код
                $codeInFields = preg_replace('/{{=([^}]+)}}/', '$1', strip_tags($value));
                // Заменяем ключ данных на сами данные
                if (array_key_exists($codeInFields, $this->fields) && $this->fields[$codeInFields]) {
                    $result = str_replace($value, $this->fields[$codeInFields], $result);
                }
            }
        } else {
            // Заменяем ключ данных на сами данные без использования регулярок
            $result = str_replace(array_keys($this->fields), array_values($this->fields), $result);
        }
        return $result;
    }

    /**
     * В массиве входных полей заменяет концы строк на соответствующие в ворде
     * @param array $fields массив входных полей
     *
     * @return array
     */
    protected function replaceNewLines(array $fields):array {
        if ($fields) {
            foreach ($fields as $code=>$value) {
                if (is_array($value)) {
                    // Рекурсивная замена
                    $fields[$code] = $this->replaceNewLines($value);
                } else {
                    $fields[$code] = str_replace("\n", '<w:br/>', $value);
                }
            }
        }
        return $fields;
    }

    /**
     * Поиск ключей входных данных в входящем тексте
     * @param $template
     *
     * @return array
     */
    protected function getReplacers(string $template): array
    {
        preg_match_all("/({[^}]*{[^}]*=[^}]+}[^}]*})/ms", $template, $matches);
        $result = [];
        foreach ($matches[0] as $match) {
            $result[] = $match;
        }
        return $result;
    }
}