<?php

namespace app\common\exception;

/**
 * 参数异常处理类
 */
class ParameterException extends CommonException
{
    /**
     * @var array 异常参数数据
     */
    private $parameterData;

    /**
     * ParameterException constructor.
     * @param string $message
     * @param string $parameterName
     * @param mixed  $parameterValue
     * @param string $reason
     */
    public function __construct(string $message = 'Parameter abnormal', string $parameterName = '', $parameterValue = null, string $reason = '')
    {
        parent::__construct($message);

        $this->parameterData = [
            'Error ParameterName' => $parameterName,
            'Error ParameterValue' => $parameterValue,
            'Error Reason' => $reason
        ];
    }

    /**
     * @title getParameterData
     * @return array
     */
    public function getParameterData(): array
    {
        return $this->parameterData;
    }
}
