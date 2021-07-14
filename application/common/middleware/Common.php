<?php
/**
 * 应用通用中间件
 */

namespace app\common\middleware;

use think\Request;
use app\common\controller\CommonController;
use app\common\exception\CommonException;
use app\common\exception\ControllerException;
use app\common\exception\ServiceException;
use app\common\exception\ModelException;
use app\common\exception\ParameterException;
use app\common\exception\UnauthorizedException;

class Common
{
    /**
     * @title handle
     * @param Request  $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        try {
            $response = $next($request);
        } catch (UnauthorizedException $e) {
            return json(CommonController::returnJson($e->getStatusCode(), $e->getMessage()), $e->getStatusCode());
        } catch (ParameterException $e) {
            return json(CommonController::returnError($e->getMessage(), $e->getParameterData()));
        } catch (ControllerException $e) {
            return json(CommonController::returnError($e->getMessage(), $e->getData()));
        } catch (ServiceException $e) {
            return json(CommonController::returnError($e->getMessage(), $e->getReturnData()));
        } catch (ModelException $e) {
            return json(CommonController::returnError($e->getMessage(), $e->getData()));
        } catch (CommonException $e) {
            return json(CommonController::returnError($e->getMessage(), $e->getData()));
        }
        return $response;
    }
}
