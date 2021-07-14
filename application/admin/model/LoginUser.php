<?php

namespace app\admin\model;

use app\common\model\common\Common;

/**
 * 登录用户模型类
 * Class AdminUser
 * @package app\admin\model
 */
class LoginUser extends Common
{
    /**
     * @var string
     */
    protected $name = 'admin_user';

    /**
     * @title findByUsername
     * @param string $username
     * @return array
     */
    public static function findByUsername(string $username): array
    {
        return self::getInfo('*', ['username' => $username]);
    }

    /**
     * @title findByUsername
     * @param string $username
     * @return array
     */
    public static function findById(int $id): array
    {
        return self::getInfo('*', ['id' => $id]);
    }

    /**
     * @title findPasswordById
     * @param int $id
     * @return string
     */
    public static function findPasswordById(int $id): string
    {
        return self::getValue('password', ['id' => $id]);
    }
}
