<?php

namespace app\admin\model;

class Demo extends \app\common\model\Demo
{
    const COMMENT = 'Demo';
    protected $observerClass = event\Log::class;
}
