<?php

namespace app\admin\service;

use think\Container;
use think\facade\Cache;
use app\admin\model\LoginUser as LoginUserModel;
use app\admin\traits\Token;
use app\common\service\CommonService;
use app\common\exception\ParameterException;
use app\common\exception\UnauthorizedException;
use app\admin\model\AdminRole;
use app\admin\utils\Password;
use app\admin\entity\LoginUser;

class UserLoginService extends CommonService implements interfaces\UserLogin
{
    use Token;

    // 密码错误锁定条件（次数）（0-永不锁定）
    const LOGIN_LIMIT = 5;
    // 登录失败锁定时间（min）（0-永久锁定）
    const LOCK_TIME = 600;
    // 缓存时间
    const CACHE_TIME = 86400;
    // 用户信息缓存前缀
    const CACHE_PREFIX = 'ADMIN_USER_';

    /**
     * @title login
     * @param string $username
     * @param string $password
     * @return array
     * @throws \app\common\exception\ModelException
     * @throws \app\common\exception\ServiceException
     */
    public function login(string $username, string $password): array
    {
        // 查询用户信息
        $userInfo = LoginUserModel::findByUsername($username);

        // 账号不存在
        if (!$userInfo) {
            $this->exception('账号或密码错误');
        }

        // 是否在职
        if (!$userInfo['status']) {
            $this->exception('账号或密码错误');
        }

        // 账号已锁定
        if ($userInfo['is_lock']) {
            // 登录失败锁定时间（min）（0-永久锁定）
            $lockTime = self::LOCK_TIME;
            // 永久锁定
            if (!$lockTime) {
                $this->exception('帐号已锁定');
            }
            // 限时锁定
            $unlockTime = $userInfo['last_fail_time'] + $lockTime; // 解锁时间
            // 未到解锁时间
            if ($unlockTime > time()) {
                $this->exception('帐号已锁定');
            }
            // 已到解锁时间，重置登录失败次数
            $userInfo['login_fail_times'] = 0;
        }

        // 密码错误
        if (Password::encrypt($password) != $userInfo['password']) {
            // 密码错误锁定条件（次数）（0-永不锁定）
            $times = self::LOGIN_LIMIT;
            if (!$times) { // 不锁定账号，返回密码错误
                $this->exception('账号或密码错误');
            }

            // 待锁定
            $updateData = [
                'is_lock' => 0, // 默认不锁定
                'last_fail_time' => time(), // 最后登录失败时间
                'login_fail_times' => $userInfo['login_fail_times'] + 1 // 登录失败次数（包括本次）
            ];
            // 离锁定剩余次数 = 登录失败锁定条件（次数） - 登录失败次数 ( <= 0 ：锁定账号)
            $leftTimes = $times - $updateData['login_fail_times'];
            if ($leftTimes > 0) {
                // 添加失败次数
                $updateRes = LoginUserModel::updateByPk($updateData, $userInfo['id']);
                $error = '账号或密码错误';
            } else {
                // 锁定账号
                $updateData['is_lock'] = 1;
                $updateRes = LoginUserModel::updateByPk($updateData, $userInfo['id']);
                $error = '帐号已锁定';
            }
            // 更新数据失败，返回登录失败
            if (!$updateRes) {
                $this->exception($error);
            } else {
                $this->exception('登录失败');
            }
        }

        // 登录成功
        $updateData = [
            'last_login' => time(),
            'last_ip' => request()->ip(),
            'last_fail_time' => 0,
            'login_fail_times' => 0,
            'is_lock' => 0
        ];
        LoginUserModel::updateByPk($updateData, $userInfo['id'], true);

        // 整合最新用户信息
        $userInfo = array_merge($userInfo, $updateData);

        // 设置授权信息
        $this->setAuthorizationInfo($userInfo);

        // 获取token
        $token = self::getToken($userInfo['id'], $userInfo['last_login']);

        // 添加登录日志
        AdminLogService::logLogin($userInfo['id']);

        return ['token' => $token];
    }

    /**
     * @title logout
     * @throws ParameterException
     */
    public function logout()
    {
        self::destroyToken(self::getRequestToken());
    }

    /**
     * @title checkLogin
     * @throws UnauthorizedException
     */
    public function checkLogin()
    {
        $userId = self::checkToken(self::getRequestToken());
        if (!$userId) {
            throw new UnauthorizedException();
        }

        // 从缓存获取
        $loginUser = self::getUserCache($userId);
        if (!$loginUser) {
            // 从数据库获取
            $userInfo = LoginUserModel::findById($userId);
            if (!$userInfo) {
                throw new UnauthorizedException();
            }

            // 设置授权信息
            $this->setAuthorizationInfo($userInfo);
        } else {
            LoginUser::resetInstance($loginUser);
        }
    }

    /**
     * @title getLoginUserInfo
     * @return array
     */
    public function getLoginUserInfo(): array
    {
        return LoginUser::getInstance()->toArray();
    }

    /**
     * @title 刷新登录用户信息
     * @throws UnauthorizedException
     */
    public function refreshLoginUserInfo()
    {
        $userInfo = LoginUserModel::findById(LoginUser::getUserId());
        if (!$userInfo) {
            throw new UnauthorizedException();
        }
        $this->setAuthorizationInfo($userInfo);
    }

    /**
     * @title 设置授权信息
     * @param array $userInfo
     */
    public function setAuthorizationInfo(array $userInfo)
    {
        // 获取用户角色
        $roleInfo = AdminRole::findById($userInfo['role_id']);
        // 获取用户权限
        $permissionInfo = AdminPermissionService::getMenuAndPermissionInfo(
            $roleInfo['permission'],
            $userInfo['role_id'] == LoginUser::getSuperAdministratorId() ? true : false // 超管获取所有
        );

        // 设置并缓存登录用户对象
        $loginUser = LoginUser::setInstance(
            $userInfo['id'],
            $userInfo['name'],
            [$roleInfo['name']],
            $roleInfo['id'],
            $permissionInfo['menu'],
            $permissionInfo['permission']
        );
        self::updateUserCache($loginUser);
    }

    /**
     * @title getRequestToken
     * @return string
     */
    public static function getRequestToken(): string
    {
        return Container::get('request')->header('token', '');
    }

    /**
     * @title 更新用户缓存数据
     * @param LoginUser $userInfo 用户信息
     */
    public static function updateUserCache(LoginUser $loginUser)
    {
        Cache::set(self::CACHE_PREFIX . $loginUser->getId(), $loginUser, self::CACHE_TIME);
    }

    /**
     * @title 获取缓存用户数据
     * @param string $id 用户id
     * @return LoginUser|null
     */
    public static function getUserCache(string $id)
    {
        return Cache::get(self::CACHE_PREFIX . $id);
    }
}
