<?php

namespace Squirrel\Mink\SocketIo;

use Behat\Mink\Driver\NodeJS\Connection;
use Behat\Mink\Driver\NodeJS\Server as NodeJSServer;

class Server extends NodeJSServer
{

    private $httpPort  = 8000;

    public function setHttpPort($port)
    {
      $this->httpPort = $port;
    }

    protected function doEvalJS(Connection $conn, $str, $returnType = 'js')
    {

        $result = null;
        switch ($returnType) {
            case 'js':
                $result = $conn->socketSend("stream.end(JSON.stringify($str))");
                break;
            case 'json':
                $result = json_decode($conn->socketSend("stream.end(JSON.stringify({$str}))"));
                break;
            default:
                $result = $conn->socketSend($str);
                break;
        }
        return $result;
    }

    protected function getServerScript()
    {
        $js = <<<'JS'
var net      = require('net')
  , socketio = require('socket.io-client')
  , buffer   = ""
  , host     = '%host%'
  , port     = %port%;

var connections = [];

var debug = "";

var newConnection = function(address, query, eventName) {
  var connection  = {},
      socket      = socketio.connect(address, {'force new connection':true, 'query' : query});

  connection.connected = false;
  connection.socket = socket;
  connection.address = address;
  connection.events = {};
  var session = connections.push(connection);

  if(eventName) {
    subscribe(session, eventName);
  }
  socket.on('connect', function(){
    connection.connected = true;
  });

  return session;
}

var getConnection = function(session){
  return connections[session - 1];
}
var isConnected = function(session) {
  var conn = getConnection(session);
  return conn.connected;
}

var emit = function(session, eventName, payload) {
  var connection = getConnection(session);
  var socket = connection.socket;
  socket.emit(eventName, payload);
}

var subscribe = function(session, eventName) {
  var connection = getConnection(session);
  var socket = connection.socket;

  if( connection.events[eventName] !== undefined) {
    return;
  }

  var eventStore = [];
  socket.on(eventName, function(data){
    eventStore.push(data);
  });

  connection.events[eventName] = eventStore;
}

var peek = function(session, eventName) {
  var connection = getConnection(session);

  return connection.events[eventName];
}


net.createServer(function (stream) {
  stream.setEncoding('utf8');
  stream.allowHalfOpen = true;
  buffer   = "";
  stream.on('data', function (data) {
    buffer += data;
  });

  stream.on('end', function () {
    eval(buffer);
  });

}).listen(port, host, function() {
  console.log('server started on ' + host + ':' + port);
});

JS;
        return $js;
    }
}
