<?php

namespace app\common\utils;

use think\facade\Config;
use think\facade\Lang;

class Language
{
    /**
     * @title 获取当前语言字段后缀
     * @return string
     */
    public static function getSuffix(): string
    {
        switch (self::getCurrent()) {
            case 'en-us':
                return '_eng';
            case 'zh-tw':
                return '';
            case 'zh-cn':
                return '';
            default:
                return '';
        }
    }

    /**
     * @title 获取当前语言
     * @return string
     */
    public static function getCurrent(): string
    {
        // 允许的语言
        $allowLangList = Config::get('allow_lang_list', ['zh-cn', 'zh-tw', 'en-us']);

        // 当前语言
        $lang = Lang::range();

        // 校验语言是否允许，若不允许则使用默认语言
        if (!in_array($lang, $allowLangList)) {
            $lang = Config::get('default_lang');
        }

        return $lang;
    }
}
