<?php
include "./conndb.php";

$db_link = db_conn();  //데이터베이스와 연결하는 사용자 정의 함수
mysqli_select_db($db_link,$DB_SNAME); //내부 database 선택

session_cache_limiter('private_no_expire, must-revalidate');
session_start();
?>

<!--CSS-->
<link rel="stylesheet" href="css/style.css" type="text/css">
<link rel="stylesheet" href='css/fullcalendar.min.css' type="text/css">
<link rel="stylesheet" href='css/fullcalendar.print.min.css' media='print' type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- fa fa icon-->
<link rel="stylesheet" href="css/bootstrap.css">
<!--JS-->

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery-1.7.min.js"></script>
<script type="text/javascript" src="js/jquery.rwdImageMaps.min.js"></script>
<script type="text/javascript" src='js/moment.min.js'></script>
<script type="text/javascript" src='js/fullcalendar.min.js'></script>
<script type="text/javascript" src='js/fullcalendar_ko.js'></script>
<script type="text/javascript" src="js/jquery.bpopup2.min.js" type="text/javascript"></script>
<script type="text/javascript" src='js/visual.js'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<head>
    <meta charset="utf-8">
    <title>ABOV 인사평가 시스템</title>
</head>

<!---------------------------------------------------------------- // 헤드영역 끝 ----------------------------------------------------------->

<div style = "margin-bottom:20px;"> </div>