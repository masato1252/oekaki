<?php

$team_num = $_POST["team_num"];
if(!$team_num || $team_num=="" || $team_num==-1){
    printf("<h2>回答者番号を選んで下さい。</h2>");
    printf("<a href='./'>＜＜戻る</a>");
    exit();
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <title>クライアント</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <link rel="stylesheet" href="./css/theme_client.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-min.js" type="text/javascript"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="/socket.io/socket.io.js" type="text/javascript"></script>
    <script type="text/javascript">

var team_num = <?php printf($_POST["team_num"]); ?>;

var socket = io();

socket.on("connected", function () {});  // 接続時
socket.on("disconnect", function (client) {}); // 切断時

socket.emit('sendTeamNumToServer', team_num);

weightDef = new Array();
weightDef[0] = 4;
weightDef[1] = 10;
weightDef[2] = 15;
currentWeight = 1;

$(function() {
	var offset = 5;
    var fromX;
    var fromY;
    var drawFlag = false;
    var context = $("canvas").get(0).getContext('2d');

    $('canvas').get( 0 ).width = $( window ).width()*0.95;
    $('canvas').get( 0 ).height = $( window ).height()*0.8;

    $('.colors').width($( window ).width()/10);
    $('.colors').height($( window ).width()/10);
    $('.colors-none').width($( window ).width()/10);
    $('.colors-none').height($( window ).width()/10);

    $('.weights').width($( window ).width()/10);
    $('.weights').height($( window ).width()/10);
    $('.weights-none').width($( window ).width()/10);
    $('.weights-none').height($( window ).width()/10);

    $('#c-black').css("border", "7px solid #FF8000");
    $('#weight-m').css("border", "7px solid #FF8000");

    var width = $("canvas").width();
    var height = $("canvas").height();
    
    var myStyle = context.strokeStyle;
    var myWidth = weightDef[currentWeight];

    // サーバからメッセージ受信
    socket.on('sendEachDrawToClient', function (msg) {
        context.setTransform((width/msg.width), 0, 0, (height/msg.height), 0, 0);
        context.strokeStyle = msg.color;
        context.lineWidth = msg.line;
        context.beginPath();
        context.moveTo(msg.fx, msg.fy);
        context.lineTo(msg.tx, msg.ty);
        context.stroke();
        context.closePath(); 
    });


    socket.on('sendAllClearToClient', function () {
        context.setTransform(1, 0, 0, 1, 0, 0);
        context.clearRect(0, 0, $('canvas').width(), $('canvas').height());
    });

    socket.on('sendEachClearToClient', function () {
        context.setTransform(1, 0, 0, 1, 0, 0);
        context.clearRect(0, 0, $('canvas').width(), $('canvas').height());
    });


//----------------------------
// スマートフォン用タッチイベント
//----------------------------

$('canvas').bind('touchstart', function() {
	drawFlag = true;
	event.preventDefault();
	fromX = event.changedTouches[0].pageX - $(this).offset().left - offset;
	fromY = event.changedTouches[0].pageY - $(this).offset().top - offset;

    context.beginPath();
    context.moveTo(fromX, fromY);

    //90度回転させてサーバへ送信
    socket.emit('sendDrawDownToServer', { team_num:team_num, width:height, height:width, fx:height-fromY, fy:fromX });
    //回転させない場合は、以下のコードを利用
    //socket.emit('sendDrawDownToServer', { team_num:team_num, width:height, height:width, fx:fromX, fy:fromY });

	return false;  // for chrome
});

$('canvas').bind('touchmove', function() {
	event.preventDefault();
	if (drawFlag) {
	    draw(event.changedTouches[0].pageX, event.changedTouches[0].pageY);
	}
});

$('canvas').bind('touchend', function() {
    context.closePath();

    socket.emit('sendDrawUpToServer', { team_num:team_num });

	drawFlag = false;
});


//-----------------------
// パソコン用タッチイベント
//-----------------------

$('canvas').bind('mousedown', function() {
	drawFlag = true;
	event.preventDefault();
	fromX = event.pageX - $(this).offset().left - offset;
	fromY = event.pageY - $(this).offset().top - offset;

    context.beginPath();
    context.moveTo(fromX, fromY);

    //90度回転させてサーバへ送信
    socket.emit('sendDrawDownToServer', { team_num:team_num, width:height, height:width, fx:height-fromY, fy:fromX });
    //回転させない場合は、以下のコードを利用
    //socket.emit('sendDrawDownToServer', { team_num:team_num, width:height, height:width, fx:fromX, fy:fromY });

	return false;  // for chrome
});

$('canvas').bind('mousemove', function() {
	event.preventDefault();
	if (drawFlag) {
	    draw(event.pageX, event.pageY);
	}
});

$('canvas').bind('mouseup', function() {
    context.closePath();

    socket.emit('sendDrawUpToServer', { team_num:team_num });

	drawFlag = false;
});


 
$('span').click(function(e) {

    if($(this).attr("class")=="colors"){

        if($(this).attr("id")=="eraser"){
            //消しゴム
            context.strokeStyle = $(this).css('background-color');
            myStyle = context.strokeStyle;
            myWidth = 60;
            $('.colors').css("border", "1px solid #EEEEEE");
            $(this).css("border", "7px solid #FF8000");
        
        }else if($(this).attr("id")=="reset"){

            socket.emit('sendClearToServer', team_num);
            e.preventDefault();
            context.setTransform(1, 0, 0, 1, 0, 0);
            context.clearRect(0, 0, $('canvas').width(), $('canvas').height());

        }else{
            context.strokeStyle = $(this).css('background-color');
            myStyle = context.strokeStyle;
            myWidth = weightDef[currentWeight];
            $('.colors').css("border", "1px solid #EEEEEE");
            $(this).css("border", "7px solid #FF8000");
        }

    }else if($(this).attr("class")=="weights"){

        if($(this).attr("id")=="weight-l"){
            currentWeight = 2;
        }else if($(this).attr("id")=="weight-m"){
            currentWeight = 1;
        }else if($(this).attr("id")=="weight-s"){
            currentWeight = 0;
        }
        myWidth = weightDef[currentWeight];
        $('.weights').css("border", "1px solid #EEEEEE");
        $(this).css("border", "7px solid #FF8000");
    }

});
 

$('#clear').click(function(e) {
    socket.emit('sendClearToServer', team_num);
    e.preventDefault();
    context.setTransform(1, 0, 0, 1, 0, 0);
    context.clearRect(0, 0, $('canvas').width(), $('canvas').height());
});
 


    function draw(x, y) {
        var toX = x - $('canvas').offset().left - offset;
        var toY = y - $('canvas').offset().top - offset;
        context.setTransform(1, 0, 0, 1, 0, 0);
        context.strokeStyle = myStyle;
        context.lineWidth = myWidth;
        context.lineTo(toX, toY);
        context.stroke();
 
        //90度回転させてサーバへ送信
        socket.emit('sendDrawToServer', { team_num:team_num, width:height, height:width, fx:height-fromY, fy:fromX, tx:height-toY, ty:toX, color:context.strokeStyle, line:context.lineWidth });
        //回転させない場合は、以下のコードを利用
        //socket.emit('sendDrawToServer', { team_num:team_num, width:height, height:width, fx:fromX, fy:fromY, tx:toX, ty:toY, color:context.strokeStyle, line:context.lineWidth });

        fromX = toX;
        fromY = toY;
    }
 

 });

    </script>
</head>
<body>
<div class="layerImage">
<div class="layerTransparent">
<div id="wrapper"> 
    <div class="header">
        <span class="colors-none"><?php printf($_POST["team_num"]); ?></span>
        <span class="colors" id="c-black" style="background-color:#000"></span>
        <span class="colors" id="c-red" style="background-color:#f00"></span>
        <span class="colors" id="c-green" style="background-color:#04B404"></span>
        <span class="colors" id="c-blue" style="background-color:#00f"></span>
        <span class="colors" id="c-yellow" style="background-color:#D7DF01"></span>
        <span class="colors" id="eraser" style="background-color:#fff"></span>
        <span class="colors-none"></span>
        <span class="colors" id="reset" style="background-color:#fff"></span>
    </div>
    <div class="clear"></div>


    <canvas width="800" height="400"></canvas>

    <div class="footer">
        <span class="weights-none"></span>
        <span class="weights" id="weight-l"></span>
        <span class="weights-none"></span>
        <span class="weights" id="weight-m"></span>
        <span class="weights-none"></span>
        <span class="weights" id="weight-s"></span>
        <span class="weights-none"></span>
        <span class="weights-none"></span>
        <span class="weights-none"></span>
    </div>
</div><!-- wrapper -->
</div>
</div>
</body>
</html>