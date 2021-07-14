<?php

namespace app\common\exception;

/**
 * 未授权异常处理类
 */
class UnauthorizedException extends CommonException
{
    /**
     * @var int 状态码
     */
    private $statusCode;

    /**
     * UnauthorizedException constructor.
     * @param string|null $message
     * @param int         $statusCode
     */
    public function __construct(string $message = null, int $statusCode = 401)
    {
        parent::__construct($message ?? 'Unauthorized');

        $this->statusCode = $statusCode;
    }

    /**
     * @title getStatusCode
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
