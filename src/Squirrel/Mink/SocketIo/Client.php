<?php

namespace Squirrel\Mink\SocketIo;

use Squirrel\Mink\SocketIo\Server;
use Behat\Mink\Session;

class Client
{

    public $session;

    /**
     * __construct
     * @param Server $server
     */
    public function __construct($server)
    {
        $this->server = $server;
    }

    public function connect($host, $timeout = null, $eventName = null, $user = null)
    {
        $query = "";
        if($user){
            $query = "user=$user";
        }
        if($eventName == null) {
            $js = "newConnection('$host', '$query')";
        }else{
            $js = "newConnection('$host', '$query', '$eventName')";
        }
        $this->session = $this->server->evalJs("$js");

        if($timeout !== null){
            if(! $this->isConnected($timeout) ) {
                throw new \Exception("Not connected after {$timeout} seconds");
            }
        }
    }

    private function wait($wait = null) {
        if($wait !== null ){
            $uwait = $wait * 1000000;
            usleep($uwait);

        }
    }

    private function isConnected($wait = null)
    {
        $js = <<<JS
isConnected(
    $this->session
)
JS;
        $this->wait($wait);

        return json_decode($this->server->evalJs("$js"));
    }

    public function emit($eventName, $payload)
    {
        $js = <<<JS
emit(
    $this->session,
    '$eventName',
    $payload
)
JS;
        var_Dump($this->server->evalJs($js));
    }


    public function subscribe($eventName)
    {
        $js = <<<JS
subscribe(
    $this->session,
    '$eventName'
)
JS;
            $this->server->evalJs($js);
    }

    public function peek($eventName)
    {
        $js = <<<JS
peek(
    $this->session,
    '$eventName'
)
JS;
        return $this->server->evalJs($js);
    }

    public function peekAfter($eventName, $wait = 1)
    {
        $this->wait($wait);
        return $this->peek($eventName);
    }
}
