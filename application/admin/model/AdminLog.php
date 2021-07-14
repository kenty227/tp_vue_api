<?php

namespace app\admin\model;

use app\common\model\common\Common as CommonModel;

class AdminLog extends CommonModel
{
    /**
     * 操作类型
     */
    const TYPE = [
        'DEFAULT' => 0,
        'INSERT' => 10,
        'UPDATE' => 20,
        'DELETE' => 30
    ];

    /**
     * @title getTypeAttr
     * @param int $type
     * @return string
     */
    public static function getTypeAttr(int $type): string
    {
        $typeName = [
            self::TYPE['DEFAULT'] => '',
            self::TYPE['INSERT'] => '新增',
            self::TYPE['UPDATE'] => '编辑',
            self::TYPE['DELETE'] => '删除'
        ];
        return $typeName[$type] ?? $typeName[self::TYPE['DEFAULT']];
    }

    /**
     * @title add
     * @param string $tableName
     * @param string $tableId
     * @param int    $type
     * @param string $detail
     */
    public static function add(string $tableName, string $tableId, int $type = 0, string $detail = '')
    {
        $dataType = is_scalar($tableId) ? 1
            : (is_array($tableId) && count($tableId) > 1 ? 2 : 1);

        $data = [
            'user_id' => \app\admin\entity\LoginUser::getUserId(),
            'date_type' => $dataType,
            'table_name' => $tableName,
            'table_id' => $tableId,
            'type' => $type,
            'detail' => $detail
        ];
        self::create($data);
    }
}
