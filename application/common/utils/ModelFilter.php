<?php

namespace app\common\utils;

class ModelFilter
{
    /**
     * @title getCommonQueryConditions
     * @param array $filter
     * @param array $filterParam
     * @return array
     */
    public static function getCommonQueryConditions(array $filter, array $filterParam = []): array
    {
        $map = [];

        if (empty($filterParam)) {
            return $map;
        }

        /**
         * $filter 结构
         * [
         *      ['数据表字段名', '查询条件', '请求参数名'],
         *      ......
         * ]
         */
        foreach ($filter as $f) {
            // 校验数组结构
            if (!is_array($f) || count($f) != 3) {
                continue;
            }

            $key = $f[2];

            // 校验请求参数数组中是否有该参数
            if (!isset($filterParam[$key]) || $filterParam[$key] === '') {
                continue;
            }

            if ($f[1] === 'like') {
                $f[2] = "%{$filterParam[$key]}%";
            } elseif ($f[1] === 'between_date') {
                if (is_string($filterParam[$key])) {
                    $f[1] = 'between';
                    $f[2] = [
                        strtotime($filterParam[$key]),
                        strtotime('+1day-1second', strtotime($filterParam[$key]))
                    ];
                } elseif (is_array($filterParam[$key]) && count($filterParam[$key]) == 2) {
                    $f[1] = 'between';
                    $f[2] = [
                        strtotime($filterParam[$key][0]),
                        strtotime('+1day-1second', strtotime($filterParam[$key][1]))
                    ];
                } else {
                    continue;
                }
            } elseif ($f[1] === 'between_time') {
                if (!is_array($filterParam[$key]) || count($filterParam[$key]) != 2) {
                    continue;
                }
                $f[1] = 'between';
                $f[2] = [strtotime($filterParam[$key][0]), strtotime($filterParam[$key][1])];
            } else {
                $f[2] = $filterParam[$key];
            }

            $map[] = $f;
        }

        return $map;
    }

    /**
     * @title getQueryConditionsExceptEmpty
     * @param array $filter
     * @param array $filterParam
     * @return array
     */
    public static function getQueryConditionsExceptEmpty(array $filter, array $filterParam = []): array
    {
        $map = [];

        if (empty($filterParam)) {
            return $map;
        }

        /**
         * $filter 结构
         * [
         *      ['数据表字段名', '查询条件', '请求参数名'],
         *      ......
         * ]
         */
        foreach ($filter as $f) {
            // 校验数组结构
            if (!is_array($f) || count($f) != 3) {
                continue;
            }

            $key = $f[2];

            // 校验请求参数数组中是否有该参数
            if (empty($filterParam[$key])) {
                continue;
            }

            if ($f[1] === 'like') {
                $f[2] = "%{$filterParam[$key]}%";
            } elseif ($f[1] === 'between_date') {
                if (!is_string($filterParam[$key])) {
                    continue;
                }
                $f[1] = 'between';
                $f[2] = [
                    strtotime($filterParam[$key]),
                    strtotime('+1day-1second', strtotime($filterParam[$key]))
                ];
            } elseif ($f[1] === 'between_time') {
                if (!is_array($filterParam[$key]) || count($filterParam[$key]) != 2) {
                    continue;
                }
                $f[1] = 'between';
                $f[2] = [strtotime($filterParam[$key][0]), strtotime($filterParam[$key][1])];
            } else {
                $f[2] = $filterParam[$key];
            }

            $map[] = $f;
        }

        return $map;
    }
}
