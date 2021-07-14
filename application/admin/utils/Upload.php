<?php

namespace app\admin\utils;

use think\Container;
use app\common\exception\FileException;

class Upload
{
    /**
     * @var \think\File
     */
    private $file;
    /**
     * @var array
     */
    private static $requireExt = [
        'excel' => [
            'ext' => [
                'xls',
                'xlsx'
            ]
        ]
    ];

    /**
     * Upload constructor.
     * @param string $name
     * @throws FileException
     */
    public function __construct(string $name = 'file')
    {
        $this->file = Container::get('request')->file($name);

        if (!$this->file) {
            throw new FileException('请上传文件');
        }
    }

    /**
     * @title getFileTmpPath
     * @param string $fileType
     * @return string
     * @throws FileException
     */
    public function getFileTmpPath(string $fileType): string
    {
        if (!$this->file->validate(self::$requireExt[$fileType])) {
            throw new FileException('上传文件类型错误', self::$requireExt[$fileType]);
        }

        return $this->file->getInfo('tmp_name');
    }

    /**
     * @title getFileName
     * @return string
     */
    public function getFileName(): string
    {
        return $this->file->getInfo('name');
    }
}
