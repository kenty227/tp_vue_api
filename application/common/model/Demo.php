<?php

namespace app\common\model;

use app\common\utils\Upload;

class Demo extends common\Common
{
    /**
     * @title getContentAttr
     * @param $value
     * @return string
     */
    public function getContentAttr($value): string
    {
        return stripslashes(htmlspecialchars_decode($value));
    }

    /**
     * @title setImageAttr
     * @param $value
     * @return string
     */
    public function setImageAttr($value): string
    {
        return Upload::getSavePath($value);
    }

    /**
     * @title getImageAttr
     * @param $value
     * @return string
     */
    public function getImageAttr($value): string
    {
        return Upload::getAccessUrl($value);
    }
}
