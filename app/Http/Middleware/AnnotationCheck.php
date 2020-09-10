<?php

namespace App\Http\Middleware;

use App\Annotations\Permission;
use App\Http\Controllers\ResponseHelper;
use Closure;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class AnnotationCheck
{
    use ResponseHelper;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        if (empty($route)) {
            return $this->respond(500, '路由信息为空');
        }
        $controller = $route->getController();
        try {
            // 反射获取目标控制器对象
            $class = new \ReflectionClass($controller);
            // 反射获取目标控制器的目标方法
            $method = $class->getMethod($route->getActionMethod());
            AnnotationRegistry::registerFile(app_path('Annotations/Permission.php'));
            $reader = new AnnotationReader();
            foreach ($reader->getMethodAnnotations($method) as $annotation) {
                if ($annotation instanceof Permission) {
                    if (!auth()->check()) {
                        return $this->respond(401, '用户未登录');
                    }
                    if (strlen($annotation->action) == 0) {
                        return $next($request);
                    }
                    return auth()->user()->can($annotation->action) ? $next($request) : $this->respond(403, '权限不足');
                }
            }
            return $next($request);
        } catch (\Exception $e) {
            return $this->respond(500, $e->getMessage());
        }
    }
}
