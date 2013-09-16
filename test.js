var net      = require('net')
  , http     = require('http')
  , browser  = null
  , pointers = []
  , buffer   = ""
  , host     = '127.0.0.1'
  , port     = 5555;

net.createServer(function (stream) {
  stream.setEncoding('utf8');
  stream.allowHalfOpen = true;

  stream.on('data', function (data) {
    buffer += data;
  });

  stream.on('end', function () {
    if (browser == null) {
      browser = new zombie.Browser();

      // Clean up old pointers
      pointers = [];
    }

    eval(buffer);
    buffer = "";
  });
}).listen(port, host, function() {
  console.log('server started on ' + host + ':' + port);
});

http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/plain'});
  res.end('rob');
}).listen('9001',function() {
  console.log('http server started on 9001');
});