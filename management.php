<?php
    include "./gnb.php";

	if(!$_SESSION['sess_userid']) { //로그인하지 않았다면 로그인 페이지로 이동
		?>
			<script>
				location.replace("index.php");
			</script>
		<?
		exit;
	}
if($_SESSION['sess_grade'] < 1) { //관리자 권한확인
		?>
			<script>
				location.replace("index.php");
			</script>
		<?
		exit;
	}

	//조직인사평가
	$t_id = $_SESSION['sess_username'];
	$T_SQL = "SELECT * FROM eval_user WHERE user_id LIKE '%$t_id%'";
	$tsql_query = mysqli_query($db_link, $T_SQL);
    while($trow = mysqli_fetch_array($tsql_query)) {
        $user_group = $trow[user_group];
		$user_team = $trow[user_team];
    }

	// 사원 관리
    $SQL = "SELECT * FROM eval_user WHERE user_group LIKE '%abov%' AND user_team LIKE '%$user_team%' ORDER BY user_use DESC,user_group asc";
    $sql_query = mysqli_query($db_link, $SQL);
    while($row = mysqli_fetch_array($sql_query)) {

		if($row['user_use'] == 1){
			$use_yn = "재직중";
		} else {
			$use_yn = "퇴사";
		}

		$sum_sql = "SELECT sum(score) AS tot_score FROM tb_evaluation WHERE user_id = '$row[user_id]'";
		$sql_query1 = mysqli_query($db_link, $sum_sql);
		$sum = mysqli_fetch_array($sql_query1);

		$tbody_html_user .=  "<tr>
								<td><a href = \"./mypage.php?uid=$row[user_id]\">$row[user_id]</a></td>
								<td>$row[user_name]</td>
								<td>$row[user_no]</td>
								<td>$sum[tot_score]</td>
								<td>$row[user_rank]</td>
								<td>$row[user_group]</td>
								<td>$row[last_login]</td>
								<td>$use_yn</td>
							</tr>";
    }

?>
<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header">조직 인사평가</h3>
	</div>
		<div class = "e_mid">
		<div class = "body_table">
			<table class = "tbl_score" style = "text-align:center;">
					<colgroup>
						<col style="width:10%;">
						<col style="width:15%;">
						<col style="width:5%;">
						<col style="width:10%;">
						<col style="width:5%;">
						<col style="width:25%;">
						<col style="width:15%;">
						<col style="width:10%;">
					</colgroup>
					<thead>
						<tr>
							<th style = "text-align:center;">ID</th>
							<th style = "text-align:center;">이름</th>
							<th style = "text-align:center;">사번</th>
							<th style = "text-align:center;">종합평가점수</th>
							<th style = "text-align:center;">직무등급</th>
							<th style = "text-align:center;">소속그룹</th>
							<th style = "text-align:center;">마지막로그인</th>
							<th style = "text-align:center;">사용여부</th>
						</tr>
					</thead>
					<tbody>
						<?=$tbody_html_user?>
					</tbody>
			</table>
		</div>
	</div>
</div>
</body>
</html>
<? include "./foot.php";?>
<style>
	.e_body{margin:40px;}
	.h3_header{font-size:22px; font-weight:600; color:#01324b;}
	.b_sum{float:right; margin:20px 0px;}
	/* table {width:100%; border:1px solid black; border-collapse:collapse;}
	th, td{border:1px solid black; height:40px; padding-left:10px;} */
	table{width:100%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:40px;}
	.sync{background-color:#214796; height:35px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px; float:right; margin-bottom:15px;}
	.listdown{background-color:#214796; height:35px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px; float:right; margin-bottom:15px;margin-right:5px;}
</style>