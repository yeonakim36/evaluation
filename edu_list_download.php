<?php
include "./head.lib.php";
if(!$_SESSION['sess_userid']) { //로그인하지 않았다면 로그인 페이지로 이동
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
if($_SESSION['sess_manage'] != 1) { //관리자 권한확인
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
include_once "./conndb.php";
 $today = date("Ymd");
 $xls_name = "education_list_$today";
 header( "Content-type: application/vnd.ms-excel" );
 header( "Content-type: application/vnd.ms-excel; charset=utf-8");
 header( "Content-Disposition: attachment; filename=$xls_name.xls");
 header( "Content-Description: PHP4 Generated Data" );

    // 테이블 상단 만들기
    $EXCEL_FILE = "
    <table border = '1'>
        <tr>
            <td>교육대상</td>
            <td>이름</td>
            <td>소속그룹</td>
            <td>교육일자</td>
            <td>교육과정</td>
            <td>교육기관</td>
            <td>수료여부</td>
            <td>교육일수</td>
            <td>교육시간</td>
        </tr>
    ";
    $db_link = db_conn();
	$SQL = " SELECT  el.*, eu.user_name AS eu_user_name, el.user_name AS el_user_name, eu.user_group, eu.user_team 
            FROM education_list el left JOIN eval_user eu 
            ON el.user_id = eu.user_id 
            WHERE el.`use` = 1
            ORDER BY edu_type desc, result desc";
    $sql_query = mysqli_query($db_link, $SQL);
    
    //테이블 내용 만들기
    while($row = mysqli_fetch_array($sql_query)){

        $user_id = $row["user_id"];
		$el_user_name = $row["el_user_name"];
		$eu_user_name = $row["eu_user_name"];
		$user_name = $row["user_name"];
		$user_group = $row["user_group"];
		$user_team = $row["user_team"];
        $edu_stdate = $row["edu_startdate"];
		$edu_eddate = $row["edu_enddate"];
		$edu_name = $row["edu_name"];
		$edu_institution = $row["edu_institution"];
		$result = $row["result"];
		$edu_period = $row["edu_period"];
		$edu_time = $row["edu_time"];
		$edu_id = $row["edu_id"];
		
		if($result == 0){
			$rst_txt = "미수료";
			if($el_user_name == "all"){
				$user_id = "전사교육";
				$user_name = "-";
				$user_group = "-";
				$rst_txt = "-";
			} else if ($el_user_name == "executives"){
				$user_id = "임원교육";
				$user_name = "-";
				$user_group = "-";
				$rst_txt = "-";
			} else if ($el_user_name == "teamleader"){
				$user_id = "팀장교육";
				$user_name = "-";
				$user_group = "-";
				$rst_txt = "-";
			} 
			$EXCEL_FILE .=  "<tr>
									<td style=\"text-align:center;\">$user_id</td>
									<td style=\"text-align:center;\">$user_name</td>
									<td style=\"text-align:center;\">$user_group</td>
									<td style=\"text-align:center;\">$edu_stdate~$edu_eddate</td>
									<td style=\"text-align:center;\">$edu_name</td>
									<td style=\"text-align:center;\">$edu_institution</td>
									<td style=\"text-align:center;\">$rst_txt</td>
									<td style=\"text-align:center;\">$edu_period</td>
									<td style=\"text-align:center;\">$edu_time</td>
								</tr>";
		} else {
			$rst_txt = "수료";
			$EXCEL_FILE .=  "<tr style = 'cursor:pointer;' onclick = \"location.href='./education_survey_detail.php?eid=$edu_id&uid=$user_id'\">
									<td style=\"text-align:center;\">$user_id</td>
									<td style=\"text-align:center;\">$user_name</td>
									<td style=\"text-align:center;\">$user_group</td>
									<td style=\"text-align:center;\">$edu_stdate~$edu_eddate</td>
									<td style=\"text-align:center;\">$edu_name</td>
									<td style=\"text-align:center;\">$edu_institution</td>
									<td style=\"text-align:center;\">$rst_txt</td>
									<td style=\"text-align:center;\">$edu_period</td>
									<td style=\"text-align:center;\">$edu_time</td>
								</tr>";
		}
    }

    $EXCEL_FILE.="</table>";
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>"; 
    echo $EXCEL_FILE;
?>
