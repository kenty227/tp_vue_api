<?php

namespace app\common\utils;

use think\facade\Config;

/**
 * 前台数据缓存工具类（不能用于后台数据，例如后台用户登录token等数据）
 * Class Cache
 * @package app\common\utils
 * @method \Redis handler() static 返回句柄对象，可执行其它高级方法（此处仅用于当使用Redis作缓存时作快速定位使用）
 * @method void addTagItem(string ...$name) static 添加缓存键至缓存标签
 * @method void delTagItem(string ...$name) static 添加缓存键至缓存标签
 * @method array getItemList(string $tag) static 获取所有缓存数据
 */
class Cache extends \think\facade\Cache
{
    /**
     * 前台缓存数据标签名（用于区别后台缓存数据）
     */
    const TAG_NAME = 'tag_app';
    /**
     * 缓存键名常量
     */
    const WECHAT_APP = [
        'ACCESS_TOKEN' => 'wechat_app_access_token'
    ];
    const STATISTICS = [
        'ACTIVITY_USER_DAILY' => 'statistics_activity_user_'
    ];
    const ACTIVITY = [
        'INDEX_LIST' => 'activity_index_list',
        'DETAIL' => 'activity_detail_',
        'QUESTION_DETAIL' => 'activity_question_detail_',
        'T_C_ID' => 'activity_t_c_id_',
    ];
    const TERMS_CONDITIONS = [
        'AUTHORIZED_REGISTRATION' => 'terms_conditions_authorized_registration',
        'MEMBER_REGISTRATION' => 'terms_conditions_member_registration',
        'E_MEMBER_REGISTRATION' => 'terms_conditions_e_member_registration',
        'ACTIVITY_COMMON' => 'terms_conditions_activity_common',
        'DETAIL' => 'terms_conditions_detail_'
    ];
    const BANNER = [
        'INDEX' => 'banner_index'
    ];
    const CONFIG = [
        'SHARE' => 'config_share'
    ];
    const QUESTIONS = [
        'LIST_BY_ACTIVITY' => 'questions_list_activity_',
        'SET' => 'questions_set_'
    ];
    const AREA = [
        'MIDDLE_LIST' => 'area_middle_list_',
        'FULL_LIST' => 'area_full_list'
    ];
    const USER = [
        'PHONE' => 'user_phone_'
    ];

    /**
     * @Override
     * 重写 \think\Facade 类方法
     */
    public static function __callStatic($method, $params)
    {
        $cacheDriver = static::createFacade();

        // 每次调用缓存驱动类静态方法前设置标签名称
        $cacheDriver->tag(self::TAG_NAME);

        return call_user_func_array([$cacheDriver, $method], $params);
    }

    /**
     * @title clear
     * @param string|null $tag
     * @return bool|mixed
     */
    public static function clear(string $tag = null)
    {
        $params = [is_null($tag) ? self::TAG_NAME : $tag];

        return self::__callStatic(__FUNCTION__, $params);
    }

    /**
     * @title getKey
     * @param string $key
     * @return string
     */
    public static function getKey(string $key): string
    {
        return self::getPrefix() . $key;
    }

    /**
     * @title getPrefix
     * @return string
     */
    public static function getPrefix(): string
    {
        $prefix = Config::get('cache.prefix');
        substr($prefix, -1) !== ':' && $prefix .= ':';
        return $prefix;
    }
}
