<?php
if(!$_SESSION['sess_userid']) { //로그인하지 않았다면 로그인 페이지로 이동
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
if($_SESSION['sess_grade'] != 1) { //관리자 권한확인
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
include_once "./conndb.php";
 $today = date("Ymd");
 $xls_name = "employee_list_$today";
 header( "Content-type: application/vnd.ms-excel" );   
 header( "Content-type: application/vnd.ms-excel; charset=utf-8");  
 header( "Content-Disposition: attachment; filename=$xls_name.xls");
 header( "Content-Description: PHP4 Generated Data" ); 

    // 테이블 상단 만들기
    $EXCEL_FILE = "
    <table border = '1'>
        <tr>
            <td>ID</td>
            <td>이름</td>
            <td>사번</td>
            <td>종합평가점수</td>
            <td>직무등급</td>
            <td>소속그룹</td>
            <td>마지막로그인</td>
            <td>사용여부</td>
        </tr>
    ";
    $db_link = db_conn();
	$SQL = " SELECT * FROM eval_user WHERE user_group LIKE '%abov%' ORDER BY user_use DESC,user_group asc";
    $sql_query = mysqli_query($db_link, $SQL);
    
    //테이블 내용 만들기
    while($row = mysqli_fetch_array($sql_query)){

        if($row['user_use'] == 1){
			$use_yn = "재직중";
		} else {
			$use_yn = "퇴사";
		}

		$sum_sql = "SELECT sum(score) AS tot_score FROM tb_evaluation WHERE user_id = '$row[user_id]'";
		$sql_query1 = mysqli_query($db_link, $sum_sql);
		$sum = mysqli_fetch_array($sql_query1);

        $EXCEL_FILE.="
            <tr>
                <td>".$row['user_id']."</td>
                <td>".$row['user_name']."</td>
                <td>".$row['user_no']."</td>
                <td>".$sum['tot_score']."</td>
                <td>".$row['user_rank']."</td>
                <td>".$row['user_group']."</td>
                <td>".$row['last_login']."</td>
                <td>".$use_yn."</td>
            </tr>
        ";
    }

    $EXCEL_FILE.="</table>";
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>"; 
    echo $EXCEL_FILE;
?>
