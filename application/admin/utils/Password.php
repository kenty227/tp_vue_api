<?php

namespace app\admin\utils;

class Password
{
    /**
     * @title 用户密码加密方法
     * @param string $str     加密的字符串
     * @param string $authKey 加密符
     * @return string         加密后长度为32的字符串
     */
    public static function encrypt(string $str, string $authKey = ''): string
    {
        return ($str === '') ? '' : md5(sha1($str) . $authKey);
    }
}
