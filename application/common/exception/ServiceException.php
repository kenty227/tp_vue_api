<?php

namespace app\common\exception;

/**
 * 服务相关异常处理类
 */
class ServiceException extends CommonException
{
    /**
     * @var array
     */
    protected $returnData = [];

    /**
     * ServiceException constructor.
     * @param string $message
     * @param array  $data
     */
    public function __construct(string $message, array $data = [])
    {
        parent::__construct($message);

        if (count($data) == count($data, 1)) {
            // 返回参数
            $this->setReturnData($data);
        } else {
            // 额外参数
            foreach ($data as $label => $d) {
                $this->setData($label, $d);
            }
        }
    }

    /**
     * @title setReturnData
     * @param array $data
     */
    protected function setReturnData(array $data)
    {
        $this->returnData = $data;
    }

    /**
     * @title getReturnData
     * @return array
     */
    public function getReturnData(): array
    {
        return $this->returnData;
    }
}
