<?php

namespace app\admin\controller;

use app\common\controller\CommonController;
use app\admin\service\interfaces\UserLogin;
use app\admin\service\LoginUserService;
use app\admin\service\UserLoginService;

/**
 * 登录用户接口（所有已登录用户可用，无需验证相关接口权限）
 * Class LoginUser
 * @package app\admin\controller
 */
class LoginUser extends CommonController
{
    /**
     * @title 获取登录用户信息
     * @param UserLogin $userLogin
     * @return array
     */
    public function info(UserLogin $userLogin): array
    {
        return $this->returnSuccessData($userLogin->getLoginUserInfo());
    }

    /**
     * @title 刷新登录用户信息
     * @param UserLoginService $userLoginService
     * @return array
     * @throws \app\common\exception\UnauthorizedException
     */
    public function refresh(UserLoginService $userLoginService): array
    {
        $userLoginService->refreshLoginUserInfo();
        return $this->returnSuccess();
    }

    /**
     * @title 登出
     * @param UserLogin $userLogin
     * @return array
     */
    public function logout(UserLogin $userLogin): array
    {
        $userLogin->logout();

        return $this->returnSuccess();
    }

    /**
     * @title updatePwd
     * @param LoginUserService $loginUserService
     * @param string           $oldPassword
     * @param string           $newPassword
     * @param string           $repeatNewPassword
     * @return array
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     */
    public function updatePwd(
        LoginUserService $loginUserService,
        string $oldPassword,
        string $newPassword,
        string $repeatNewPassword
    ): array {
        $loginUserService->updatePassword($oldPassword, $newPassword, $repeatNewPassword);

        return $this->returnSuccess();
    }
}
