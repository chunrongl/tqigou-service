<?php
namespace Chunrongl\TqigouService\Services;


use Chunrongl\TqigouService\Exceptions\BusinessException;
use Chunrongl\TqigouService\Exceptions\InvalidConfigException;
use Hprose\Swoole\Server;

class Socket
{
    private $clients;

    public function register()
    {
        $uri = $this->getRealListen();

        /** @var \Hprose\Socket\Server $server */
        $server = new Server($uri, SWOOLE_PROCESS);

        $server->setErrorTypes(E_ALL);
        $server->setDebugEnabled(false);

        $server->addInvokeHandler(array($this, "middleHandle"));

        $server->onSendError = function ($error, \stdClass $context) {
            //            \Log::error($error);
            $message = "Exception:" . $error->getMessage() . " in " . $error->getFile() . ":" . $error->getLine() . " called client:" . $this->tqigou_client_user;
            \Log::error($message);
            throw new BusinessException($error->getMessage(), $error->getCode());
        };

        return $server;
    }

    private function getRealListen()
    {
        $uri = config('tqigou-rpc-server.uris');

        if (empty($uri)) {
            throw new InvalidConfigException('配置监听地址非法', 500);
        }

        $realUri = "tcp://" . ltrim($uri, "tcp://");

        return $realUri;
    }

    public function middleHandle($name, array &$args, \stdClass $context, \Closure $next)
    {

        $this->validateAuth($args, $context);

        $result = $next($name, $args, $context);
        return $result;
    }

    private function validateAuth(&$args)
    {
        $this->clients = null;
        $clientMemo = end($args);
        if (isset($clientMemo['tqigou_client_version']) && $clientMemo['tqigou_client_version'] >= config('tqigou-rpc-server.auth_version')) {

            if ($clientMemo['tqigou_client_secret'] !== config('tqigou-rpc-server.secret')) {
                throw new InvalidConfigException("Illegal calls Tqigou-server", 500);
            }

            array_pop($args);

            $this->clients = $clientMemo;
        }

    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        if (isset($this->clients[$name])) {
            return $this->clients[$name];
        }

        return null;
    }

}