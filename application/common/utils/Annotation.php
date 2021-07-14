<?php

namespace app\common\utils;

class Annotation
{
    /**
     * @title getStringValue
     * @param string      $tag
     * @param string      $className
     * @param string|null $methodName
     * @return string
     * @throws \ReflectionException
     */
    public static function getStringValue(string $tag, string $className, string $methodName = null): string
    {
        if (!is_null($methodName)) {
            $comment = (new \ReflectionMethod($className, $methodName))->getDocComment();
        } else {
            $comment = (new \ReflectionClass($className))->getDocComment();
        }

        return self::getStringValueByComment($comment, $tag);
    }

    /**
     * @title getLine
     * @param string      $tag
     * @param string      $className
     * @param string|null $methodName
     * @return string
     * @throws \ReflectionException
     */
    public static function getLine(string $tag, string $className, string $methodName = null): string
    {
        if (!is_null($methodName)) {
            $comment = (new \ReflectionMethod($className, $methodName))->getDocComment();
        } else {
            $comment = (new \ReflectionClass($className))->getDocComment();
        }

        return self::getLineByComment($comment, $tag);
    }

    /**
     * @title getStringValue
     * @param string $comment
     * @param string $tag
     * @return string
     */
    public static function getStringValueByComment(string $comment, string $tag): string
    {
        $comment = self::parseComment($comment, $tag);

        $result = preg_match("/{$tag}\s?\(\s?[\'\"]([\-\_\/\:\<\>\?\$\[\]\w]+)[\'\"]\s?\)/is", $comment, $m);

        return ($result && !empty($m[1])) ? $m[1] : '';
    }

    /**
     * @title getLine
     * @param string $comment
     * @param string $tag
     * @return string
     */
    public static function getLineByComment(string $comment, string $tag): string
    {
        return self::parseComment($comment, $tag);
    }

    /**
     * @title parseComment
     * @param string $comment 注释内容
     * @param string $tag     需要解释的标签名称（不含 @ ( ) 等符号）
     * @return string
     */
    private static function parseComment(string $comment, string $tag): string
    {
        if (strpos($tag, '@') !== 0) {
            $tag = '@' . $tag;
        }

        $comment = substr($comment, 3, -2);
        $comment = explode(PHP_EOL, substr(strstr(trim($comment), $tag), 1));
        $comment = array_map(function($item) {
            return trim(trim($item), ' \t*');
        }, $comment);

        if (count($comment) > 1) {
            $key = array_search('', $comment);
            $comment = array_slice($comment, 0, false === $key ? 1 : $key);
        }

        $comment = implode(PHP_EOL . "\t", $comment) . ';';

        if (strpos($comment, '{')) {
            $comment = preg_replace_callback('/\{\s?.*?\s?\}/s', function($matches) {
                return false !== strpos($matches[0], '"') ? '[' . substr(var_export(json_decode($matches[0], true), true), 7, -1) . ']' : $matches[0];
            }, $comment);
        }

        return $comment;
    }
}
