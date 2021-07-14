<?php

namespace app\admin\service\interfaces;

/**
 * Interface UserLogin
 * @package app\admin\service\interfaces
 */
interface UserLogin
{
    /**
     * @title login
     * @param string $username
     * @param string $password
     * @return array
     */
    public function login(string $username, string $password): array;

    /**
     * @title logout
     */
    public function logout();

    /**
     * @title checkLogin
     */
    public function checkLogin();

    /**
     * @title getLoginUserInfo
     * @return array
     */
    public function getLoginUserInfo(): array;
}
