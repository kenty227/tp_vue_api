<?php

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Container;

class Permission extends Command
{
    protected $permission = [];

    /**
     * @title configure
     */
    protected function configure()
    {
        $this->setName('optimize:permission')
            ->setDescription('Build permission cache.');
    }

    /**
     * @title execute
     * @param Input  $input
     * @param Output $output
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {
        $app = Container::get('app');

        $basePath = $app->getAppPath();

        $namespace = $app->getNameSpace();

        $suffix = Container::get('config')->get('controller_suffix') || Container::get('config')->get('class_suffix');

        $layer = $app->config('app.url_controller_layer');

        if ($app->config('app.app_multi_module')) {
            $modules = glob($basePath . '*', GLOB_ONLYDIR);

            foreach ($modules as $module) {
                $module = basename($module);

                if (in_array($module, $app->config('app.deny_module_list'))) {
                    continue;
                }

                $path = $basePath . $module . DIRECTORY_SEPARATOR . $layer . DIRECTORY_SEPARATOR;
                $this->buildDirPermission($path, $namespace, $module, $suffix, $layer);
            }
        } else {
            $path = $basePath . $layer . DIRECTORY_SEPARATOR;
            $this->buildDirPermission($path, $namespace, '', $suffix, $layer);
        }

        // 生成文件
        $filename = $app->getRuntimePath() . 'build_permission.php';
        $content = '<?php ' . PHP_EOL . '// 根据 Annotation 自动生成的权限规则' . PHP_EOL;
        if ($this->permission) {
            $content .= 'return ' . var_export($this->permission, true) . ';' . PHP_EOL;
        }
        file_put_contents($filename, $content);

        $output->writeln("Successed");
    }

    /**
     * 生成子目录控制器类的权限规则
     * @param string $path      控制器目录
     * @param string $namespace 应用命名空间
     * @param string $module    模块
     * @param bool   $suffix    类库后缀
     * @param string $layer     控制器层目录名
     */
    protected function buildDirPermission(string $path, string $namespace, string $module, bool $suffix, string $layer)
    {
        $controllers = glob($path . '*.php');

        foreach ($controllers as $controller) {
            $controller = basename($controller, '.php');

            $class = new \ReflectionClass($namespace . '\\' . ($module ? $module . '\\' : '') . $layer . '\\' . $controller);

            if (strpos($layer, '\\')) {
                // 多级控制器
                $level = str_replace(DIRECTORY_SEPARATOR, '.', substr($layer, 11));
                $controller = $level . '.' . $controller;
                $length = strlen(strstr($layer, '\\', true));
            } else {
                $length = strlen($layer);
            }

            if ($suffix) {
                $controller = substr($controller, 0, -$length);
            }

            $this->getControllerPermission($class, $module, $controller);
        }

        $subDir = glob($path . '*', GLOB_ONLYDIR);

        foreach ($subDir as $dir) {
            $this->buildDirPermission($dir . DIRECTORY_SEPARATOR, $namespace, $module, $suffix, $layer . '\\' . basename($dir));
        }
    }

    /**
     * 生成控制器类的权限规则
     * @param \ReflectionClass $class      控制器完整类名
     * @param string           $module     模块名
     * @param string           $controller 控制器名
     */
    protected function getControllerPermission(\ReflectionClass $class, string $module, string $controller)
    {
        $comment = $class->getDocComment();

        if (false !== strpos($comment, '@permission(')) {
            $comment = $this->getPermissionComment($comment);
            if ($comment) {
                // 添加控制器权限
                $this->permission[$module][$controller] = $comment;
            }
        }

        // 生成方法的权限注释
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $this->getMethodPermission($module, $controller, $method);
        }
    }

    /**
     * 获取方法的权限注释
     * @param string            $module     模块
     * @param string            $controller 控制器名
     * @param \ReflectionMethod $reflectMethod
     */
    protected function getMethodPermission(string $module, string $controller, \ReflectionMethod $reflectMethod)
    {
        $comment = $reflectMethod->getDocComment();

        if (false !== strpos($comment, '@permission(')) {
            $comment = $this->getPermissionComment($comment);
            if ($comment) {
                $action = $reflectMethod->getName();
                if ($suffix = Container::get('app')->config('app.action_suffix')) {
                    $action = substr($action, 0, -strlen($suffix));
                }
                // 添加控制器方法权限
                $this->permission[$module][$controller][$action] = $comment;
            }
        }
    }

    /**
     * 获取权限注释
     * @param string $comment
     * @param string $tag
     * @return string
     */
    protected function getPermissionComment(string $comment, string $tag = '@permission('): string
    {
        // 解析权限注释
        $comment = $this->parsePermissionComment($comment, $tag);

        // 获取权限字符串
        $result = preg_match('/permission\s?\(\s?[\'\"]([\-\_\/\:\<\>\?\$\[\]\w]+)[\'\"]\s?\)/is', $comment, $m);

        return ($result && !empty($m[1])) ? $m[1] : '';
    }

    /**
     * 解析权限注释
     * @param string $comment
     * @param string $tag
     * @return string
     */
    protected function parsePermissionComment(string $comment, string $tag = '@permission('): string
    {
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
