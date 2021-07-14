<?php

namespace app\common\exception;

/**
 * 文件异常处理类
 */
class FileException extends CommonException
{
    /**
     * FileException constructor.
     * @param string $message
     * @param array  $data
     */
    public function __construct(string $message, array $data = [])
    {
        parent::__construct($message);

        // 额外参数
        foreach ($data as $label => $d) {
            $this->setData($label, $d);
        }
    }
}
