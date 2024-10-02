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
 $xls_name = "employee_score_list_$today";
 header( "Content-type: application/vnd.ms-excel" );   
 header( "Content-type: application/vnd.ms-excel; charset=utf-8");  
 header( "Content-Disposition: attachment; filename=$xls_name.xls");
 header( "Content-Description: PHP4 Generated Data" ); 

    // 테이블 상단 만들기
    $EXCEL_FILE = "
    <table border = '1'>
        <tr>
            <td>사번</td>
            <td>ID</td>
            <td>이름</td>
            <td>년도</td>
            <td>분기</td>
            <td>종합평가</td>
            <td>평가점수</td>
            <td>상사코멘트</td>
            <td>비고</td>
        </tr>
    ";
    $db_link = db_conn();
	$SQL = "SELECT * FROM tb_evaluation ORDER BY user_no";
    $sql_query = mysqli_query($db_link, $SQL);
    
    //테이블 내용 만들기
    while($row = mysqli_fetch_array($sql_query)){

        $EXCEL_FILE.="
            <tr>
                <td>".$row['user_no']."</td>
                <td>".$row['user_id']."</td>
                <td>".$row['user_name']."</td>
                <td>".$row['year']."</td>
                <td>".$row['half']."</td>
                <td>".$row['total_grade']."</td>
                <td>".$row['score']."</td>
                <td>".$row['comment']."</td>
                <td>".$row['etc']."</td>
            </tr>
        ";
    }

    $EXCEL_FILE.="</table>";
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>"; 
    echo $EXCEL_FILE;
?>
