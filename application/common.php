<?php
// +----------------------------------------------------------------------
// | 应用公共文件（项目公共函数）
// +----------------------------------------------------------------------

/**
 * @title 调试方法
 * @param mixed $data 输出数据
 * @param bool  $exit 是否终止程序
 */
function p($data, bool $exit = true)
{
    if ($exit) {
        if (is_array($data) || is_scalar($data)) {
            echo json_encode($data);
        } else {
            print_r($data);
        }
        exit;
    }
    dump($data);
}

/**
 * @title 格式化日期
 * @param int    $timestamp
 * @param string $format
 * @return string
 */
function formatDate(int $timestamp, string $format = 'Y-m-d'): string
{
    return formatDateTime($timestamp, $format);
}

/**
 * @title 格式化日期时间
 * @param int    $timestamp
 * @param string $format
 * @return string
 */
function formatDateTime(int $timestamp, string $format = 'Y-m-d H:i:s'): string
{
    return empty($timestamp) ? '' : date($format, $timestamp);
}
