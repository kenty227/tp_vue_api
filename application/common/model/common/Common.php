<?php

namespace app\common\model\common;

use think\Db;
use think\Model;
use app\common\driver\paginator\Vue;
use app\common\exception\ModelException;

class Common extends Model
{
    /**
     * @var bool 是否自动写入时间戳
     */
    protected $autoWriteTimestamp = true;
    /**
     * 错误信息
     */
    const ERROR_MESSAGE = [
        'PARAMETER_ERROR' => '参数异常',
        'EMPTY_DATA' => '暂无数据',
        'INSERT_FAIL' => '新增数据失败',
        'UPDATE_FAIL' => '更新数据失败',
        'DELETE_FAIL' => '删除数据失败'
    ];

    /**
     * @title getInstance
     * @return static
     */
    public static function getInstance(): self
    {
        return new static();
    }

    /**
     * @title findSearchList
     * @param string $labelField
     * @param string $keyField
     * @param array  $map
     * @return array
     */
    public static function findSearchList(string $labelField, string $keyField = 'id', array $map = []): array
    {
        return self::findAllList("{$keyField} as id, {$labelField} as name", $map);
    }

    /**
     * @title findAllList
     * @param string|array $field 筛选字段
     * @param array        $map   筛选条件
     * @param string       $order 排序
     * @param string       $alias 别名
     * @param array        $join  关联条件
     * @return array
     */
    public static function findAllList(
        $field = '*',
        array $map = [],
        string $order = '',
        string $alias = '',
        array $join = []
    ): array {
        return self::findList($field, $map, $order, null, null, $alias, $join);
    }

    /**
     * @title findPaginateList
     * @param string|array $field 筛选字段
     * @param array        $map   筛选条件
     * @param string       $order 排序
     * @param int|null     $limit 每页条数
     * @param string       $alias 别名
     * @param array        $join  关联条件
     * @return array
     */
    public static function findPaginateList(
        $field = '*',
        array $map = [],
        string $order = '',
        int $limit = null,
        string $alias = '',
        array $join = []
    ): array {
        return self::findList($field, $map, $order, null, $limit, $alias, $join, true);
    }

    /**
     * @title 获取列表数据
     * @param string|array $field    筛选字段
     * @param array        $map      筛选条件
     * @param string       $order    排序
     * @param int|null     $page     页数
     * @param int|null     $limit    每页条数
     * @param string       $alias    别名
     * @param array        $join     关联条件
     * @param bool         $paginate 是否使用paginate分页查询
     * @return array
     */
    public static function findList(
        $field = '*',
        array $map = [],
        string $order = '',
        int $page = null,
        int $limit = null,
        string $alias = '',
        array $join = [],
        bool $paginate = false
    ): array {
        $model = self::getInstance();

        $model = $model->field($field);

        $alias && $model = $model->alias($alias);

        if ($join) {
            if (count($join) == count($join, 1)) {
                $join = [$join];
            }
            $model = $model->join($join);
        }

        $model = $model->where($map);

        $order && $model = $model->order($order);

        // paginate分页
        if ($paginate) {
            // 页数：默认调用 \app\common\driver\paginator\Vue::getCurrentPage() 获取
            // 每页条数：为null则调用 \app\common\driver\paginator\Vue::getListRows() 获取
            is_null($limit) && $limit = ['list_rows' => Vue::getListRows()];
            return $model->paginate($limit)->toArray();
        }

        // page分页
        if ($page && $limit) {
            $model = $model->page($page, $limit);
        } elseif ($limit) {
            $model = $model->limit($limit);
        }
        $list = $model->select();

        return $list ? $list->toArray() : [];
    }

    /**
     * @title 获取单条数据
     * @param string|array $field 筛选字段
     * @param array        $map   筛选条件
     * @param string       $alias 别名
     * @param array        $join  关联条件
     * @return array
     */
    public static function getInfo($field = '*', array $map = [], string $alias = '', array $join = []): array
    {
        $model = self::getInstance();

        $model = $model->field($field);

        $alias && $model = $model->alias($alias);

        if ($join) {
            if (count($join) == count($join, 1)) {
                $join = [$join];
            }
            $model = $model->join($join);
        }

        return $model->where($map)->findOrEmpty()->toArray();
    }

    /**
     * @title getByPk
     * @param int          $pkValue
     * @param array|string $field
     * @return array|mixed
     */
    public static function getByPk(int $pkValue, $field = '*')
    {
        $model = self::getInstance();
        $model = $model->where($model->getPk(), $pkValue);

        $field = trim($field);
        if ((is_string($field) && $field !== '*' && strrpos($field, ',') === false) ||
            (is_array($field) && count($field) == 1)) {
            return $model->value($field);
        }

        return $model->field($field)->findOrEmpty()->toArray();
    }

    /**
     * @title 获取单条数据单个字段值
     * @param string     $field   查询字段
     * @param int|array  $map     主键值或者查询条件（闭包）
     * @param mixed|null $default 默认值
     * @return mixed|null
     */
    public static function getValue(string $field, $map, $default = null)
    {
        $model = self::getInstance();

        if (is_scalar($map)) {
            $map = [$model->getPk() => $map];
        }

        return $model->where($map)->value($field) ?? $default;
    }

    /**
     * @title isExist
     * @param int|array $map 主键值或者查询条件（闭包）
     * @return bool
     */
    public static function isExist($map): bool
    {
        $model = self::getInstance();

        if (is_scalar($map)) {
            $map = [$model->getPk() => $map];
        }

        return $model->where($map)->count('*') ? true : false;
    }

    /**
     * @title saveSingleData
     * @param array  $data    新增/更新数据
     * @param string $pkField 主键字段
     * @throws ModelException
     */
    public static function saveSingleData(array $data, string $pkField = 'id')
    {
        if (empty($data[$pkField])) {
            self::createData($data);
        } else {
            self::updateByPk($data);
        }
    }

    /**
     * @title 新增数据, 返回主键ID
     * @param array $data 新增数据
     * @return mixed
     * @throws ModelException
     */
    public static function createData(array $data)
    {
        $model = self::getInstance();

        $model->checkFunctionParameter($data);

        // 获取主键字段名称
        $pkField = $model->getPk();
        // 删除主键数据
        unset($data[$pkField]);

        // 自动写入时间戳：删除相关时间字段
        if ($model->autoWriteTimestamp === true) {
            unset($data[$model->createTime], $data[$model->updateTime]);
        }

        if (!$model->isUpdate(false)->allowField(true)->data($data, true)->save()) {
            $model->exception(self::ERROR_MESSAGE['INSERT_FAIL']);
        }

        return $model->$pkField;
    }

    /**
     * @title createAllData
     * @param array $data
     * @throws ModelException
     */
    public static function createAllData(array $data)
    {
        self::execTransaction(function () use ($data) {
            foreach ($data as $row) {
                self::createData($row);
            }
        });
    }

    /**
     * @title 根据主键更新数据
     * @param array    $data                更新数据
     * @param int|null $pkValue             主键值
     * @param bool     $isCheckAffectedRows 是否校验受影响行数（默认 不校验）
     * @throws ModelException
     */
    public static function updateByPk(array $data, int $pkValue = null, bool $isCheckAffectedRows = false)
    {
        $model = self::getInstance();

        // 获取主键字段名称
        $pkField = $model->getPk();

        if (is_null($pkValue) && isset($data[$pkField])) {
            // 从更新数据获取主键值
            $pkValue = $data[$pkField];
        } else {
            // 重新赋值主键数据
            $data[$pkField] = $pkValue;
        }

        $model->checkFunctionParameter($data, $pkValue);

        // 自动写入时间戳：删除相关时间字段
        if ($model->autoWriteTimestamp === true) {
            unset($data[$model->createTime], $data[$model->updateTime]);
        }

        // 更新数据
        if (!$model->isUpdate(true)->allowField(true)->save($data, [$pkField => $pkValue])) {
            $model->exception(self::ERROR_MESSAGE['UPDATE_FAIL']);
        }

        // 校验受影响行数
        if ($isCheckAffectedRows && !$model->getNumRows()) {
            $model->exception(self::ERROR_MESSAGE['UPDATE_FAIL']);
        }
    }

    /**
     * @title 根据主键更新数据
     * @param array $data                更新数据
     * @param array $map                 更新条件
     * @param bool  $isCheckAffectedRows 是否校验受影响行数（默认 不校验）
     * @throws ModelException
     */
    public static function updateByMap(array $data, array $map = [], bool $isCheckAffectedRows = false)
    {
        $model = self::getInstance();

        $model->checkFunctionParameter($data);

        // 删除主键数据
        unset($data[$model->getPk()]);

        // 自动写入时间戳：删除相关时间字段
        if ($model->autoWriteTimestamp === true) {
            unset($data[$model->createTime], $data[$model->updateTime]);
        }

        // 更新数据
        if (!$model->isUpdate(true)->allowField(true)->save($data, $map)) {
            $model->exception(self::ERROR_MESSAGE['UPDATE_FAIL']);
        }

        // 校验受影响行数
        if ($isCheckAffectedRows && !$model->getNumRows()) {
            $model->exception(self::ERROR_MESSAGE['UPDATE_FAIL']);
        }
    }

    /**
     * @title 根据主键删除数据
     * @param int|string|array $pkValue
     * @throws ModelException
     */
    public static function deleteByPk($pkValue)
    {
        if (!self::destroy($pkValue)) {
            self::staticException(self::ERROR_MESSAGE['DELETE_FAIL']);
        }
    }

    /**
     * @title 执行事务
     * @param \Closure $callback
     * @return bool|mixed|null|void
     * @throws ModelException
     */
    public static function execTransaction(\Closure $callback)
    {
        if (!is_callable($callback)) {
            return null;
        }

        Db::startTrans();
        try {
            // 函数返回false或抛出异常会回滚事务
            $result = call_user_func_array($callback, []);

            if ($result === false) {
                Db::rollback();
            } else {
                Db::commit();
            }

            return $result;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        } catch (\Throwable $e) {
            Db::rollback();
            self::staticException($e->getMessage());
        }
    }

    /**
     * @title 校验实例方法参数是否为空
     * @param mixed ...$parameter 待校验参数值
     * @throws ModelException
     */
    protected function checkFunctionParameter(...$parameter)
    {
        foreach ($parameter as $p) {
            if (empty($p)) {
                $this->exception(self::ERROR_MESSAGE['PARAMETER_ERROR']);
            }
        }
    }

    /**
     * @title 抛出自定义模型类异常
     * @param string $message
     * @param bool   $showErrorSql
     * @throws ModelException
     */
    protected function exception(string $message, bool $showErrorSql = false)
    {
        // 异常SQL
        $sql = $showErrorSql ? $this->getLastSql() : '';

        throw new ModelException($message, __CLASS__, $sql);
    }

    /**
     * @title staticException
     * @param string $message
     * @throws ModelException
     */
    protected static function staticException(string $message)
    {
        throw new ModelException($message, __CLASS__);
    }
}
