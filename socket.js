
var io = require('socket.io').listen(3000);
var connectionCount = 0;

// var client_event = null;
vio.sockets.on('connection', function (socket) {
    connectionCount++;


    // socket.on('client_event', function(data){
    //     client_event = data;
    // });

    socket.emit('connection_event', {connectionCount: connectionCount});

    var eventCount = 0;
    setInterval(function(){
        eventCount++;
        socket.emit('async_event', {eventCount: eventCount});
    },1000);

});
// stream.end();