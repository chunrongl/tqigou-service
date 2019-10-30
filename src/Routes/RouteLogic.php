<?php
/**
 * Created by chunrongl.
 *
 * Created on 2019-10-25 11:38
 */

namespace Chunrongl\TqigouService\Routes;


class RouteLogic
{
    const NAMESPACE='app/Http/Controllers';

    /**
     * 获取所有controller文件名.
     *
     * @return array
     */
    public function getControllers(){
        $dir=$this->getControlleUrl();
        $handle = opendir($dir . ".");
        $controllers = [];
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $controllers[] = $file; //输出文件名
            }
        }
        closedir($handle);

        return $controllers;
    }

    /**
     * 获取controller文件路径.
     *
     * @return string
     */
    private function getControlleUrl(){
        return base_path().'/'.self::NAMESPACE.'/';
    }

    /**
     * 获取真实namespace.
     *
     * @return string
     */
    public function getRealNamespace(){
        return ucfirst(str_replace('/','\\',self::NAMESPACE));
    }

    /**
     * 获取指定controller文件内的用户action.
     *
     * @param $controller
     *
     * @return null|array
     */
    public function getActions($controller){

        $file = $this->getControlleUrl() . $controller;
        if(!file_exists($file)){
            return null;
        }

        $content = file_get_contents($file);
        preg_match_all("/.*?public.*?function(.*?)\(.*?\)/i", $content, $matches);
        $actions = $matches[1];
        //排除部分方法
        $ignoreActions = array('_initialize', '__construct', '__set', 'get', '__get', '__isset', '__call','__destruct', '_empty');
        foreach ($actions as $action) {
            $action = trim($action);
            if (!in_array($action, $ignoreActions)) {
                $customerActions[] = $action;
            }
        }
        return isset($customerActions)?$customerActions:null;
    }

    public function initMethods(){
        $controllers=$this->getControllers();
        if(!$controllers){
            return null;
        }

        foreach ($controllers as $k =>$controller){

            list($className,$suffix)=explode('.',$controller);
            $accessName=substr($className,0,-10);
            
            $object=resolve(join('\\',array_filter([$this->getRealNamespace(),$className])));
            app('tqigou.server')->addInstanceMethods($object,'',$accessName);
        }
    }
}