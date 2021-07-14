<?php

namespace app\admin\model\event;

use app\admin\service\AdminLogService;
use think\Model;

/**
 * 日志记录模型事件（模型事件观察者）
 * Class Log
 * @package app\admin\model\event
 */
class Log
{
    /**
     * @title afterInsert
     * @param Model $model
     * @throws \ReflectionException
     */
    public function afterInsert(Model $model)
    {
        $pkField = $model->getPk();
        AdminLogService::logInsert($model, $model->$pkField);
    }

    /**
     * @title afterUpdate
     * @param Model $model
     * @throws \ReflectionException
     */
    public function afterUpdate(Model $model)
    {
        try {
            $pkField = $model->getPk();
            AdminLogService::logUpdate($model, $model->$pkField);
        } catch (\Exception $e) {
            \think\facade\Log::error($e->getMessage());
        }
    }

    /**
     * @title afterDelete
     * @param Model $model
     * @throws \ReflectionException
     */
    public function afterDelete(Model $model)
    {
        $pkField = $model->getPk();
        AdminLogService::logDelete($model, $model->$pkField);
    }
}
