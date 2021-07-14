<?php

namespace app\admin\service;

use app\common\service\CommonService;
use app\admin\model\LoginUser as LoginUserModel;
use app\admin\utils\Password;
use app\admin\entity\LoginUser;

/**
 * Class LoginUserService
 * @package app\admin\service
 */
class LoginUserService extends CommonService
{
    /**
     * @title updatePassword
     * @param string $oldPassword
     * @param string $newPassword
     * @param string $repeatNewPassword
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     */
    public function updatePassword(string $oldPassword, string $newPassword, string $repeatNewPassword)
    {
        $userId = LoginUser::getUserId();

        if (Password::encrypt($oldPassword) != LoginUserModel::findPasswordById($userId)) {
            $this->exception('密码错误');
        }
        if ($newPassword !== $repeatNewPassword) {
            $this->exception('两次输入密码不一致');
        }
        if ($oldPassword === $newPassword) {
            $this->exception('旧密码与新密码不能相同');
        }

        LoginUserModel::updateByPk(['password' => Password::encrypt($newPassword)], $userId);
    }
}
