<?php

namespace app\common\utils;

use think\Container;

class ModelSort
{
    /**
     * 默认排序字段参数名称
     */
    const DEFAULT_FIELD_PARAM_NAME = 'sortProp';
    /**
     * 默认排序顺序参数名称
     */
    const DEFAULT_ORDER_PARAM_NAME = 'sortOrder';
    /**
     * @var string
     */
    private $fieldParamName;
    /**
     * @var string
     */
    private $orderParamName;
    /**
     * @var string
     */
    private $defaultFieldValue;
    /**
     * @var string
     */
    private $defaultOrderValue;

    /**
     * @title 获取排序表达式（通过默认参数获取，不存在则不排序）
     * @return string
     */
    public static function getSortExpression(): string
    {
        $instance = new static();
        $instance->fieldParamName = self::DEFAULT_FIELD_PARAM_NAME;
        $instance->orderParamName = self::DEFAULT_ORDER_PARAM_NAME;

        return $instance->explainAndReturnExpression();
    }

    /**
     * @title 获取排序表达式（通过默认参数获取，不存在则使用默认排序）
     * @param string $defaultField
     * @param string $defaultOrder
     * @return string
     */
    public static function getSortExpressionAssignDefault(string $defaultField, string $defaultOrder): string
    {
        $instance = new static();
        $instance->fieldParamName = self::DEFAULT_FIELD_PARAM_NAME;
        $instance->orderParamName = self::DEFAULT_ORDER_PARAM_NAME;
        $instance->defaultFieldValue = $defaultField;
        $instance->defaultOrderValue = $defaultOrder;

        return $instance->explainAndReturnExpression();
    }

    /**
     * @title 获取排序表达式（通过指定参数获取，不存在则不排序）
     * @param string $fieldParamName
     * @param string $orderParamName
     * @return string
     */
    public static function getSortExpressionByParam(string $fieldParamName, string $orderParamName): string
    {
        $instance = new static();
        $instance->fieldParamName = $fieldParamName;
        $instance->orderParamName = $orderParamName;

        return $instance->explainAndReturnExpression();
    }

    /**
     * @title 获取排序表达式（通过指定参数获取，不存在则使用默认排序）
     * @param string $fieldParamName
     * @param string $orderParamName
     * @param string $defaultField
     * @param string $defaultOrder
     * @return string
     */
    public static function getSortExpressionByParamAndAssignDefault(string $fieldParamName, string $orderParamName,
                                                                    string $defaultField, string $defaultOrder): string
    {
        $instance = new static();
        $instance->fieldParamName = $fieldParamName;
        $instance->orderParamName = $orderParamName;
        $instance->defaultFieldValue = $defaultField;
        $instance->defaultOrderValue = $defaultOrder;

        return $instance->explainAndReturnExpression();
    }

    /**
     * @title explainAndReturnExpression
     * @return string
     */
    private function explainAndReturnExpression(): string
    {
        $request = Container::get('request');

        $field = $request->param($this->fieldParamName, $this->defaultFieldValue ?? '');
        $order = $request->param($this->orderParamName, $this->defaultFieldValue ?? '');

        return ($field && $order) ? "{$field} {$order}" : '';
    }
}
