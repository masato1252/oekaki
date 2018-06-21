
// 1.モジュールオブジェクトの初期化
var fs = require("fs");
var server = require("http").createServer(function(req, res) {
     res.writeHead(200, {"Content-Type":"text/html"});
     var output = fs.readFileSync("./index.html", "utf-8");
     res.end(output);
}).listen(3000);
var io = require("socket.io").listen(server);


// 2.イベントの定義
io.sockets.on("connection", function (socket) {

  socket.on("msg", function (data) {
  	console.log(data);
    io.sockets.emit("public", data);
  });


  // 接続開始カスタムイベント
  socket.on("connected", function () {
  });

  // 接続終了組み込みイベント
  socket.on("disconnect", function () {
  });


  //-------------------------
  // リアルタイムチャット用関数
  //-------------------------

  // メッセージ送信カスタムイベント
  socket.on("publish", function (data) {
    io.sockets.emit("publish", data);
  });




  //--------------------------------
  // リアルタイムお絵かきシステム用関数
  //--------------------------------

  socket.on('sendTeamNumToServer', function (num) {
        
        if(num==0){
        	//モニター
        	socket.join("monitor");
        }else if(num>0){
        	//クライアント
        	socket.join("team"+num);
        }
        
    });

  //Draw系
  socket.on('sendDrawToServer', function (msg) {
        // モニターへのみ送る
        socket.broadcast.to("monitor").emit('sendDrawToMonitor', msg);
        // 自分以外の同じチームへ送る
        socket.broadcast.to("team"+msg.team_num).emit('sendEachDrawToClient', msg);
    });

    socket.on('sendDrawDownToServer', function (msg) {
        // モニターへのみ送る
        socket.broadcast.to("monitor").emit('sendDrawDownToMonitor', msg);
        // 自分以外の同じチームへ送る
        socket.broadcast.to("team"+msg.team_num).emit('sendEachDrawDownToClient', msg);
    });

    socket.on('sendDrawUpToServer', function (msg) {
        // モニターへのみ送る
        socket.broadcast.to("monitor").emit('sendDrawUpToMonitor', msg);
        // 自分以外の同じチームへ送る
        socket.broadcast.to("team"+msg.team_num).emit('sendEachDrawUpToClient', msg);
    });

  	socket.on('sendAllDrawToServer', function (msg) {
        // 自分以外全員に送る
        socket.broadcast.emit('sendAllDrawToClient', msg);
    });

    socket.on('sendEachDrawToServer', function (msg) {
        // 特定のチームのみへ送る
        if(num>0){
        	socket.broadcast.to("team"+msg.team_num).emit('sendEachDrawToClient', msg);
        }
    });



  //Clear系
  socket.on('sendClearToServer', function (num) {
        // モニターへのみ送る
        socket.broadcast.to("monitor").emit('sendClearToMonitor', num);
        // 自分以外の同じチームへ送る
        socket.broadcast.to("team"+num).emit('sendEachClearToClient');
    });

  socket.on('sendAllClearToServer', function () {
        // 自分以外全員に送る
        socket.broadcast.emit('sendAllClearToClient');
        // モニターへのみ送る
        socket.broadcast.to("monitor").emit('sendAllClearToMonitor');
    });

  socket.on('sendEachClearToServer', function (num) {
        // 特定のチームのみへ送る
        if(num>0){
        	socket.broadcast.to("team"+num).emit('sendEachClearToClient');
        }
    });

  //Monitor制御系
  socket.on('sendCorrectToServer', function (msg) {
      // モニター全員へ送る
      io.sockets.to("monitor").emit('sendCorrectToMonitor', msg);
   });

  socket.on('sendCorrectClearToServer', function () {
      // モニターへのみ送る
      socket.broadcast.to("monitor").emit('sendCorrectClearToMonitor');
   });

  socket.on('sendLockToServer', function (msg) {
      // モニターへのみ送る
      socket.broadcast.to("monitor").emit('sendLockToMonitor', msg);
   });

  socket.on('sendSyncLockToServer', function () {
      // モニターへのみ送る
      socket.broadcast.to("monitor").emit('sendSyncLockToMonitor');
   });

  socket.on('sendMaskStartToServer', function () {
      // モニターへのみ送る
      socket.broadcast.to("monitor").emit('sendMaskStartToMonitor');
   });

  socket.on('sendMaskEndToServer', function () {
      // モニターへのみ送る
      socket.broadcast.to("monitor").emit('sendMaskEndToMonitor');
   });

  socket.on('sendZoomToServer', function (msg) {
      // モニターへのみ送る
      socket.broadcast.to("monitor").emit('sendZoomToMonitor', msg);
   });



});