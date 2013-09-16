<?php

namespace Tests\Squirrel\Mink\SocketIo;

use Behat\Mink\Session;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Driver\NodeJS\Connection;
use Squirrel\Mink\SocketIo\Server;
use Squirrel\Mink\SocketIo\Client;
use Tests\Squirrel\Mink\NodeJs\TestServer;


/**
 * @group unittest
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * $client socket-io client
     * @var Squirrel\Mink\SocketIo\Client
     */
    private $client;


    protected function setUp()
    {
        //setup mink deps
        //remember to start the session!
        $this->testServer = new TestServer('127.0.0.1', '9000');
        $this->testServer->start();
        $serverStr = <<<JS

var io = require('socket.io').listen(3000);
var connectionCount = 0;

var client_event = null;
io.sockets.on('connection', function (socket) {
    connectionCount++;


    socket.on('client_event', function(data){
        client_event = data;
    });

    socket.emit('connection_event', {connectionCount: connectionCount});

    var eventCount = 0;
    setInterval(function(){
        eventCount++;
        socket.emit('async_event', {eventCount: eventCount});
    },1000);

});
stream.end();
JS;
        $this->testServer->evalJs($serverStr);
        $this->server = new Server('127.0.0.1', '5555');
        $this->server->start();

        $this->client = new Client($this->server);
        return;
        
    }
    
    protected function tearDown()
    {
        $this->server->stop();
        $this->testServer->stop();
    }

    public function testConnect()
    {
        $this->client->connect('127.0.0.1:3000');
        $this->assertEquals("1", $this->client->session);
    }

    public function testConnectWithTimeout()
    {
        $this->setExpectedException('Exception');
        $this->client->connect('127.0.0.1:2000', 0.2);
    }

    public function testConnectAndSubscribe()
    {
        $this->client->connect('127.0.0.1:3000', 2, 'connection_event');
        $this->assertEquals(
            $this->client->peek('connection_event'),
            '[{"connectionCount":1}]'
        );
    }

    public function testMissEventWhenDontSubscribeOnConnection()
    {
        $this->client->connect('127.0.0.1:3000', 2);
        $this->client->subscribe('connection_event');
        $this->assertEquals(
            $this->client->peek('connection_event'),
            '[]'
        );
    }

    public function testPeekEvent()
    {
        $this->client->connect('127.0.0.1:3000', 2);
        $this->client->subscribe('async_event');
        $this->assertEquals(
            $this->client->peek('async_event'),
            '[]'
        );
    }

    public function testPeekAfter()
    {
        $this->client->connect('127.0.0.1:3000', 2);
        $this->client->subscribe('async_event');
        $this->assertEquals(
            $this->client->peekAfter('async_event',2),
            '[{"eventCount":2},{"eventCount":3}]'
        );
    }

    public function testEmitEvent()
    {
        $this->client->connect('127.0.0.1:3000', 2);
        $this->client->emit('client_event', '{"data":"client_event"}');
        sleep(2);
        $this->assertEquals(
            $this->testServer->evalJs('client_event','json'),
            '{"data":"client_event"}'
        );
    }
    
    // public function testConnectAndSubscribe()
    // {
    //     $this->client->connect('127.0.0.1:3000','server_event');
    //     echo $this->client->peek('server_event');
    // }


    // public function testSubscribeEvent()
    // {
    //     $this->client->connect('127.0.0.1:3000');
    //     $this->client->subscribe('server_event');


    // }
    

    // public function testPeekEvent()
    // {
    //     $this->client->connect('127.0.0.1:3000','server_event');
    //     $this->client->subscribe('server_event');
    //     sleep(3);
    //     $this->client->peek('server_event');
    // }



}