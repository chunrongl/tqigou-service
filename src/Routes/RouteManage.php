<?php

namespace Chunrongl\TqigouService\Routes;


class RouteManage
{
    /**
     * 添加路由.
     *
     * @param string $accessName 访问名称
     * @param string $path 'className@actionName'.
     *
     * @return $this
     */
    public function add(string $accessName, $path)
    {
        list($class,$methodName)=$this->formatPath($path);

        $this->addMethod($methodName,$class,$accessName);

        return $this;
    }

    private function formatPath($path){
        list($className,$methodName)=explode('@',$path);

        $namespaceName=app(RouteLogic::class)->getRealNamespace();

        $class=resolve(join('\\',array_filter([$namespaceName,$className])));

        return [$class,$methodName];
    }

    private function addMethod(string $method, $class, string $alias)
    {
        app('tqigou.server')->addMethod($method, $class, $alias);
    }
}