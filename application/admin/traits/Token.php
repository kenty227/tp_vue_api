<?php

namespace app\admin\traits;

use think\facade\Cache;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

/**
 * Class Token
 * @package app\admin\logic
 */
trait Token
{
    /**
     * @var int 有效期
     */
    protected static $expire = 86400;
    /**
     * @var string 签名秘钥
     */
    protected static $signKey = 'TP_BASE_ADMIN_USER_LOGIN';
    /**
     * @var string 加密方式
     */
    private static $signType = 'HS256';

    /**
     * @title 获取token
     * @param int|string $userId 用户id
     * @param int|string $time   用户登录时间
     * @return string
     */
    public static function getToken($userId, $time): string
    {
        $payload = [
            'id' => $userId,
            'login_at' => $time,
            'exp' => $time + self::$expire // Token有效期
        ];

        $token = JWT::encode($payload, self::$signKey, self::$signType);

        // 缓存token
        self::cacheToken($token, 1, self::$expire);

        return self::token_encode($token);
    }

    /**
     * @title 校验token，返回用户id
     * @param string $token
     * @return mixed
     * @throws \Exception
     */
    public static function checkToken(string $token)
    {
        $token = self::token_decode($token);

        // 验证token是否已过期
        $payload = JWT::decode($token, self::$signKey, [self::$signType]);
        if (!isset($payload->id)) {
            throw new ExpiredException('Token expired');
        }

        // 验证token是否已被销毁
        if (!self::cacheToken($token)) {
            throw new ExpiredException('Token expired');
        }

        return $payload->id;
    }

    /**
     * @title 销毁token
     * @param string $token
     * @throws \Exception
     */
    public static function destroyToken(string $token)
    {
        $token = self::token_decode($token);

        // 销毁token（删除缓存）
        JWT::decode($token, self::$signKey, [self::$signType]) && self::cacheToken($token, null);
    }

    /**
     * @title token_encode
     * @param string $token
     * @return string
     */
    protected static function token_encode(string $token)
    {
        return str_replace('.', '%', $token);
    }

    /**
     * @title token_decode
     * @param string $token
     * @return string
     * @throws \Exception
     */
    protected static function token_decode(string $token)
    {
        $tokenArray = explode('%', $token);

        if (count($tokenArray) != 4) {
            throw new \Exception('Request expired');
        }

        // 前端在token后拼接一个时间戳的加密字符串，验证请求有效期（10分钟）
        $requestTime = base64_decode(array_pop($tokenArray));
        if (!$requestTime || ($requestTime / 1000 + 600 < time())) {
            throw new \Exception('Request expired');
        }

        return implode('.', $tokenArray);
    }

    /**
     * @title cache
     * @param string   $name
     * @param mixed    $value
     * @param int|null $expire
     * @return bool|mixed
     */
    protected static function cacheToken(string $name, $value = '', int $expire = null)
    {
        $name = 'admin_token:' . $name;

        // 获取缓存数据
        if ($value === '') {
            return Cache::get($name);
        }
        // 删除缓存数据
        if (is_null($value)) {
            return Cache::rm($name);
        }
        // 缓存数据
        return Cache::set($name, $value, $expire);
    }
}
