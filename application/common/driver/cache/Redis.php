<?php

namespace app\common\driver\cache;

class Redis extends \think\cache\driver\Redis
{
    /**
     * @title getPrefix
     * @return string
     */
    protected function getPrefix(): string
    {
        substr($this->options['prefix'], -1) !== ':' && $this->options['prefix'] .= ':';

        return $this->options['prefix'];
    }

    /**
     * @title getCacheKey
     * @param string $name
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->getPrefix() . $name;
    }

    /**
     * @title getTagKey
     * @param string $tag
     * @return string
     */
    protected function getTagKey($tag)
    {
        return $this->getCacheKey('cache_tag:' . md5($tag));
    }

    /**
     * @title addTagItem
     * @param string ...$name
     */
    public function addTagItem(string ...$name)
    {
        if (!$this->tag) {
            return;
        }
        $tagName = $this->getTagKey($this->tag);

        foreach ($name as $v) {
            if (strpos($v, $this->getPrefix() !== 0)) {
                $v = $this->getCacheKey($v);
            }
            $this->handler->sAdd($tagName, $v);
        }
    }

    /**
     * @title delTagItem
     * @param string ...$name
     */
    public function delTagItem(string ...$name)
    {
        if (!$this->tag) {
            return;
        }
        $tagName = $this->getTagKey($this->tag);

        foreach ($name as $v) {
            if (strpos($v, $this->getPrefix() !== 0)) {
                $v = $this->getCacheKey($v);
            }
            $this->handler->sRem($tagName, $v);
        }
    }

    /**
     * @title getItemList
     * @param string $tag
     * @return array
     */
    public function getItemList(string $tag = ''): array
    {
        if ($tag) {
            $keyList = $this->handler->sMembers($this->getTagKey($tag));
        } else {
            $keyList = $this->handler->keys($this->getCacheKey('*'));
        }

        $list = [];
        foreach ($keyList as $fullKey) {
            $key = str_replace($this->getPrefix(), '', $fullKey);

            switch ($this->handler->type($fullKey)) {
                case \Redis::REDIS_STRING:
                    $value = $this->get($key);
                    break;
                case \Redis::REDIS_SET:
                case \Redis::REDIS_ZSET:
                    $value = $this->handler->sMembers($fullKey);
                    break;
                case \Redis::REDIS_LIST:
                    $value = $this->handler->lRange($fullKey);
                    break;
                case \Redis::REDIS_HASH:
                    $value = $this->handler->hGetAll($fullKey);
                    break;
                default:
                    $value = false;
                    break;
            }
            if ($value === false) {
                continue;
            }

            $list[] = [
                'full_key' => $fullKey,
                'key' => $key,
                'value' => $value,
                'ttl' => $this->handler->ttl($fullKey)
            ];
        }

        return $list;
    }
}
