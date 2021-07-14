<?php

namespace app\common\exception;

/**
 * 模型类相关异常处理类
 */
class ModelException extends CommonException
{
    /**
     * ModelException constructor.
     * @param string $message
     * @param string $model
     * @param string $sql
     */
    public function __construct(string $message, string $model = '', string $sql = '')
    {
        parent::__construct($message);

        if ($model === '') {
            $trace = debug_backtrace(false, 2);
            $model = $trace[1]['class'];
        }

        $this->setData('Database Status', [
            'Error Message' => $message,
            'Error Model' => $model,
            'Error SQL' => $sql
        ]);
    }
}
