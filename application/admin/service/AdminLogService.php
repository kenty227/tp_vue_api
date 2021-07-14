<?php

namespace app\admin\service;

use app\admin\model\AdminLog;
use app\common\service\CommonService;
use app\common\utils\ModelFilter;
use app\common\utils\ModelSort;
use think\helper\Str;
use think\Model;

class AdminLogService extends CommonService
{
    /**
     * @title getList
     * @param array $filter
     * @return array
     */
    public function getList(array $filter = []): array
    {
        $map = ModelFilter::getCommonQueryConditions([
            ['l.detail', 'like', 'keyword'],
        ], $filter);
        if (!empty($filter['user_id'])) {
            $map[] = ['l.user_id', '=', $filter['user_id']];
        }
        if (!empty($filter['date'])) {
            $startTime = strtotime($filter['date'][0]);
            $endTime = strtotime('+1 day -1 second', strtotime($filter['date'][1]));
            $map[] = ['l.create_time', 'between', [$startTime, $endTime]];
        }

        return AdminLog::findPaginateList(
            'l.*, u.username, u.name, r.name as role_name',
            $map,
            ModelSort::getSortExpression(),
            null,
            'l',
            [
                ['admin_user u', 'l.user_id = u.id', 'LEFT'],
                ['admin_role r', 'u.role_id = r.id', 'LEFT']
            ]);
    }

    /**
     * @title logInsert
     * @param Model     $model
     * @param int|array $tableId
     * @param string    $detail
     * @throws \ReflectionException
     */
    public static function logInsert(Model $model, $tableId, string $detail = '')
    {
        self::logCommon(AdminLog::TYPE['INSERT'], $model, $tableId, $detail);
    }

    /**
     * @title logUpdate
     * @param Model     $model
     * @param int|array $tableId
     * @param string    $detail
     * @throws \ReflectionException
     */
    public static function logUpdate(Model $model, $tableId, string $detail = '')
    {
        self::logCommon(AdminLog::TYPE['UPDATE'], $model, $tableId, $detail);
    }

    /**
     * @title logDelete
     * @param Model     $model
     * @param int|array $tableId
     * @param string    $detail
     * @throws \ReflectionException
     */
    public static function logDelete(Model $model, $tableId, string $detail = '')
    {
        self::logCommon(AdminLog::TYPE['DELETE'], $model, $tableId, $detail);
    }

    /**
     * @title logCommon
     * @param int       $type
     * @param Model     $model
     * @param int|array $tableId
     * @param string    $detail
     * @throws \ReflectionException
     */
    public static function logCommon(int $type, Model $model, $tableId, string $detail = '')
    {
        if (!in_array($type, AdminLog::TYPE)) return;

        // 获取模型类对应数据表名
        $class = new \ReflectionClass(get_class($model));
        $tableName = Str::snake($class->getShortName());

        // 操作详情
        if (!$detail) {
            $detail = AdminLog::getTypeAttr($type) . ($class->getConstant('COMMENT') ?: $tableName);
        }

        // 校验是否批量操作
        if (is_array($tableId)) {
            $tableId = json_encode($tableId);
            if (is_array($tableId) && count($tableId) > 1) {
                $detail = '批量' . $detail;
            }
        }

        AdminLog::add($tableName, $tableId, $type, $detail);
    }

    /**
     * @title logLogin
     * @param int $userId
     */
    public static function logLogin(int $userId)
    {
        AdminLog::create([
            'user_id' => $userId,
            'date_type' => 1,
            'table_name' => 'admin_user',
            'table_id' => $userId,
            'type' => AdminLog::TYPE['UPDATE'],
            'detail' => '后台用户登录'
        ]);
    }
}
