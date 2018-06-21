<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999" xml:lang="ja" lang="ja">
<head>
    <title>お絵描き</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/theme_client.css">
    <script src="/quiz/js/jquery-min.js" type="text/javascript"></script>
    <script src="./js/bootstrap.min.js"></script>
</head>
<body>
<div class="layerImage">
<div class="layerTransparent">

    <div class="row">
        <div class="col-xs-2"></div>
        <div class="col-xs-8">
            <h3>お絵描き</h3>
            <br>
            <form action="./client.php" method="post">
                <select class="form-control" name="team_num">
                    <option value="-1">---- 回答者番号 ----</option>
                    <?php
                        for($i=0; $i<4; $i++){
                            printf("<option value='".($i+1)."'>".($i+1)."</option>");
                        }
                    ?>
                </select><br><br>

                <input type="submit" class="btn btn-default" value="開始" >
            </form>
        </div>
        <div class="col-xs-2"></div>
    </div>
<br><br><br><br>
</div>
</div>
</body>
</html>