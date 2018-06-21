<?php
$width = 575;
$height = 400;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <title>モニター</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="./css/theme_monitor.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-min.js" type="text/javascript"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="/socket.io/socket.io.js" type="text/javascript"></script>
    <script type="text/javascript">

team_num = 0;

MAX_TEAM = 4;

width = 575;
height = 400;

lockCanvas = false;

socket = io();

socket.on("connected", function () {});  // 接続時
socket.on("disconnect", function (client) {}); // 切断時

socket.emit('sendTeamNumToServer', team_num);

$(function() {
	var offset = 5;
    var fromX;
    var fromY;
    var drawFlag = false;
    var context = new Array();
    for(var i=0; i<MAX_TEAM; i++){
        context[i] = $("#canvas"+(i+1)).get(0).getContext('2d');
    }
    var myStyle = context.strokeStyle;


    // サーバからメッセージ受信
    socket.on('sendDrawToMonitor', function (msg) {

        if(!lockCanvas){
            var eachContent = context[msg.team_num-1];
            eachContent.setTransform((width/msg.width), 0, 0, (height/msg.height), 0, 0);
            eachContent.strokeStyle = msg.color;
            eachContent.lineWidth = msg.line;
            eachContent.lineTo(msg.tx, msg.ty);
            eachContent.stroke();
        }
    });


    socket.on('sendDrawDownToMonitor', function (msg) {

        if(!lockCanvas){
            var eachContent = context[msg.team_num-1];
            eachContent.setTransform((width/msg.width), 0, 0, (height/msg.height), 0, 0);
            eachContent.beginPath();
            eachContent.moveTo(msg.fx, msg.fy);
        }
    });


    socket.on('sendDrawUpToMonitor', function (msg) {

        if(!lockCanvas){
            var eachContent = context[msg.team_num-1];
            eachContent.closePath(); 
        }
    });



    socket.on('sendClearToMonitor', function (num) {

        if(!lockCanvas){
            context[num-1].setTransform(1, 0, 0, 1, 0, 0);
            context[num-1].clearRect(0, 0, width, height);
        }
    });



    $('#clear').click(function(e) {

        if(!lockCanvas){
            socket.emit('sendAllClearToServer');
            e.preventDefault();
            for(var i=0; i<context.length; i++){
                context[i].setTransform(1, 0, 0, 1, 0, 0);
                context[i].clearRect(0, 0, width, height);
            }
        }
        
    });



    $('#lock').click(function(e) {
        if(lockCanvas){
            lockCanvas = false;
            $("#lockState").text("ロック解除中");
            $("#lockState").css("color", "#000000");
            socket.emit('sendLockToServer', {lockNum: 0});
        }else{
            lockCanvas = true;
            $("#lockState").text("　ロック中　");
            $("#lockState").css("color", "Red");
            socket.emit('sendLockToServer', {lockNum: 1});
        } 
    });
 

    $('#plusHeight').click(function(e) {
        var h = $("#canvas1").height();
        for(var i=0; i<MAX_TEAM; i++){
            $("#canvas"+(i+1)).height((h+10));
        }
        h = $("#cover1").height();
        for(var i=0; i<MAX_TEAM; i++){
            $("#cover"+(i+1)).height((h+10));
        }
    });

    $('#minusHeight').click(function(e) {
        var h = $("#canvas1").height();
        for(var i=0; i<MAX_TEAM; i++){
            $("#canvas"+(i+1)).height((h-10));
        }
        h = $("#cover1").height();
        for(var i=0; i<MAX_TEAM; i++){
            $("#cover"+(i+1)).height((h-10));
        }
    });
 
    $('#plusWidth').click(function(e) {
        var w = $("#canvas1").width();
        for(var i=0; i<MAX_TEAM; i++){
            $("#canvas"+(i+1)).width((w+5));
        }
        w = $("#cover1").width();
        for(var i=0; i<MAX_TEAM; i++){
            $("#cover"+(i+1)).width((w+5));
        }
    });

    $('#minusWidth').click(function(e) {
        var w = $("#canvas1").width();
        for(var i=0; i<MAX_TEAM; i++){
            $("#canvas"+(i+1)).width((w-5));
        }
        w = $("#cover1").width();
        for(var i=0; i<MAX_TEAM; i++){
            $("#cover"+(i+1)).width((w-5));
        }
    });

 });

    </script>

</head>
<body>
<div class="layerImage">
<div class="layerTransparent">
<br>
    <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-5">
            <div class="numBox" id="num1">１</div>
            <canvas id="canvas1" class="canvas" width="<?php printf($width); ?>" height="<?php printf($height); ?>"></canvas>
        </div>
        <div class="col-xs-5">
        <div class="numBox" id="num2">２</div>
            <canvas id="canvas2" class="canvas" width="<?php printf($width); ?>" height="<?php printf($height); ?>"></canvas>
        </div>
        <div class="col-xs-1"></div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-1"></div>
        <div class="col-xs-5">
            <div class="numBox" id="num3">３</div>
            <canvas id="canvas3" class="canvas" width="<?php printf($width); ?>" height="<?php printf($height); ?>"></canvas>
        </div>
        <div class="col-xs-5">
            <div class="numBox" id="num4">４</div>
            <canvas id="canvas4" class="canvas" width="<?php printf($width); ?>" height="<?php printf($height); ?>"></canvas>
        </div>
        <div class="col-xs-1"></div>
    </div>


    <div id="footer">
        <button type="button" id="clear" class="btn btn-default">全てリセット</button>
            　　　　｜　<button type="button" id="plusHeight" class="btn btn-default">高さ＋</button>
            　<button type="button" id="minusHeight" class="btn btn-default">高さー</button>
            　｜　<button type="button" id="plusWidth" class="btn btn-default">幅＋</button>
            　<button type="button" id="minusWidth" class="btn btn-default">幅ー</button>
            　｜　<button type="button" id="lock" class="btn btn-default">ロック</button>
            　　<span id="lockState">ロック解除中</span>
    </div>

</div>
</div>
</body>
</html>