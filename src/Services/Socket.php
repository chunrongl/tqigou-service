<?php
namespace Chunrongl\TqigouService\Services;


use Chunrongl\TqigouService\Exceptions\BusinessException;
use Chunrongl\TqigouService\Exceptions\InvalidConfigException;
use Hprose\Swoole\Server;

class Socket
{
    public function register(){
        $uri = $this->getRealListen();


        $server = new Server($uri, SWOOLE_PROCESS);

        $server->setErrorTypes(E_ALL);
        $server->setDebugEnabled(false);


        $server->onSendError = function ($error, \stdClass $context) {
            \Log::error($error);
            throw new BusinessException($error->getMessage(),$error->getCode());
        };

        return $server;
    }

    private function getRealListen(){
        $uri = config('tqigou-rpc-server.uris');

        if (empty($uri)) {
            throw new InvalidConfigException('配置监听地址非法', 500);
        }

        $realUri = "tcp://" . ltrim($uri, "tcp://");

        return $realUri;
    }

}