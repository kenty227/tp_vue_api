<?php

namespace app\common\exception;

/**
 * 控制器相关异常处理类
 */
class ControllerException extends CommonException
{
    /**
     * ControllerException constructor.
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
