<?php

namespace app\common\utils;

class UID
{
    /**
     * @title generateOrderNumber
     * @return string
     */
    public static function generateOrderNumber(): string
    {
        list($usec, $sec) = explode(' ', microtime());

        // YYYYMMDD-SSSSSSSS-NNNN
        $no = date('Ymd', $sec)
            . substr($sec, -5) . substr($usec, 2, 3)
            . sprintf('%04d', rand(0, 9999));

        return $no;
    }
}
