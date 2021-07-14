<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\admin\service\interfaces\UserLogin;
use app\admin\utils\Captcha;

/**
 * 基础接口（无需验证权限）
 */
class Base extends CommonController
{
    /**
     * @title login
     * @param UserLogin $userLogin 用户登录逻辑接口实现类对象
     * @param string    $username
     * @param string    $password
     * @param string    $verifyCode
     * @return array
     * @throws \app\common\exception\CaptchaException
     */
    public function login(UserLogin $userLogin, string $username, string $password, string $verifyCode = ''): array
    {
        env('captcha.is_open') && (new Captcha())->check($verifyCode);

        $data = $userLogin->login($username, $password);

        return $this->returnSuccessData($data);
    }

    /**
     * @title 获取验证码
     * @param Captcha $captcha 自定义验证码
     * @return \think\Response
     */
    public function getCaptcha(Captcha $captcha): \think\Response
    {
        return $captcha->entry();
    }
}
