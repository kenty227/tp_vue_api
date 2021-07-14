<?php

namespace app\common\exception;

use think\Exception;

/**
 * 通用异常处理类
 */
class CommonException extends Exception
{
    /**
     * CommonException constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * @title 获取自定义异常类型
     * @return string
     */
    public function getExceptionType(): string
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }
}
