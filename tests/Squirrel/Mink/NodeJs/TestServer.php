<?php

namespace Tests\Squirrel\Mink\NodeJs;

use Behat\Mink\Driver\NodeJS\Server;
use Behat\Mink\Driver\NodeJS\Connection;

class TestServer extends Server
{
    protected function doEvalJS(Connection $conn, $str, $returnType = 'js')
    {

        $result = null;
        switch ($returnType) {
            case 'js':
                $result = $conn->socketSend($str);
                break;
            case 'json':
                $result = json_decode($conn->socketSend("stream.end(JSON.stringify({$str}))"));
                break;
            default:
                break;
        }

        return $result;
    }

    protected function getServerScript()
    {
        $js = <<<'JS'

var net      = require('net')
  , buffer   = ""
  , host     = '%host%'
  , port     = %port%;

var serverCount = 0;

net.createServer(function (stream) {
  stream.setEncoding('utf8');
  stream.allowHalfOpen = true;
  stream.on('data', function (data) {
    buffer += data;
  });
  
  stream.on('end', function () {
    eval(buffer);
    buffer = "";
  });

}).listen(port, host, function() {
  console.log('server started on ' + host + ':' + port);
});

JS;
        return $js;
    }
}