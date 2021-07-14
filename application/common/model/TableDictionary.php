<?php

namespace app\common\model;

use app\common\utils\Upload;

class TableDictionary extends common\Common
{
    /**
     * @title getByTableField
     * @param string $table
     * @param string $field
     * @return array
     */
    public static function getByTableField(string $table, string $field): array
    {
        return self::findAllList('key, value', [
            'table' => $table,
            'field' => $field
        ]);
    }

    /**
     * @title getKey2Value
     * @param string $table
     * @param string $field
     * @return array
     */
    public static function getKey2Value(string $table, string $field): array
    {
        $list = self::getByTableField($table, $field);
        $data = [];
        foreach ($list as $v) {
            $data[$v['key']] = $v['value'];
        }
        return $data;
    }

    /**
     * @title getValue2Key
     * @param string $table
     * @param string $field
     * @return array
     */
    public static function getValue2Key(string $table, string $field): array
    {
        $list = self::getByTableField($table, $field);
        $data = [];
        foreach ($list as $v) {
            $data[$v['value']] = $v['key'];
        }
        return $data;
    }
}
