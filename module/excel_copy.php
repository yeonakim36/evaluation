<?php
    // include  $_SERVER['DOCUMENT_ROOT']."/gnb.php";
    
    include_once "/var/www/web/evaluation/gnb.php";


	// // 내 평가 정보
    $SQL = "SELECT * FROM eval_user";

    $sql_query = mysqli_query($db_link, $SQL);
    while($row = mysqli_fetch_array($sql_query)) {
        echo $row[user_id];
        echo "test";
    }   
?>