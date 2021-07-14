<?php

namespace app\common\utils;

class Tree
{
    /**
     * @title listToTree
     * @param array  $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int    $root
     * @return array
     */
    public static function listToTree(array $list, string $pk = 'id', string $pid = 'pid', string $child = 'children', $root = 0)
    {
        $tree = [];

        $refer = [];
        foreach ($list as $key => $value) {
            $refer[$value[$pk]] = &$list[$key];
        }

        foreach ($list as $key => $value) {
            // 判断是否存在parent
            $parentId = $value[$pid];

            if ($root == $parentId) {
                $tree[$value[$pk]] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][$value[$pk]] = &$list[$key];

                    $parent[$child] = array_values($parent[$child]);
                }
            }
        }

        return array_values($tree);
    }
}
