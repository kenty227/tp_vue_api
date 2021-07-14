<?php

namespace app\admin\utils;

use app\common\exception\FileException;

class Template
{
    /**
     * 模板文件列表
     */
    const TEMPLATE_FILE_LIST = [
        'QUESTIONS_IMPORT_' => 'questions_import_template.xlsx',
        'QUESTIONS_IMPORT_1' => 'questions_import_template_1.xlsx',
        'QUESTIONS_IMPORT_2' => 'questions_import_template_2.xlsx',
        'QUESTIONS_IMPORT_3' => 'questions_import_template_2.xlsx',
        'QUESTIONS_IMPORT_4' => 'questions_import_template_4.xlsx',
        'AREA_IMPORT' => 'area_import_template.xlsx',
        'PROBLEM_IMPORT' => 'problem_import_template.xlsx',
    ];
    /**
     * @var string
     */
    protected $templateFilePath;

    /**
     * Template constructor.
     */
    public function __construct()
    {
        $this->templateFilePath = env('root_path')
            . 'file' . DIRECTORY_SEPARATOR
            . 'template' . DIRECTORY_SEPARATOR;
    }

    /**
     * @title downloadTemplateFile
     * @param string $fileKey
     */
    public function downloadTemplateFile(string $fileKey)
    {
        $templateFile = $this->getTemplateFile($fileKey);
        download($templateFile)->send();
        exit;
    }

    /**
     * @title getTemplateFile
     * @param string $fileKey
     * @return string
     * @throws FileException
     */
    public function getTemplateFile(string $fileKey): string
    {
        if (!array_key_exists($fileKey, self::TEMPLATE_FILE_LIST)) {
            throw new FileException('模板不存在');
        }

        $templateFile = $this->templateFilePath . self::TEMPLATE_FILE_LIST[$fileKey];
        if (!file_exists($templateFile)) {
            throw new FileException('模板文件不存在');
        }

        return $templateFile;
    }
}
