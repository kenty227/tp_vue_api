<?php

namespace app\common\exception;

/**
 * 验证码异常处理类
 */
class CaptchaException extends CommonException
{
    /**
     * @var int 状态码
     */
    private $statusCode;

    /**
     * CaptchaException constructor.
     * @param string|null $message
     * @param int         $statusCode
     */
    public function __construct(string $message = null, int $statusCode = 401)
    {
        parent::__construct($message ?? 'Captcha Error!');

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
