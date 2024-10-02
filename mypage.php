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


	if( empty($_GET[uid]) ) {
		$user_id = $_SESSION['sess_username'];
	} else {
		if($_SESSION['sess_grade'] ==0) { //관리자 권한확인
			?>
				<script>
					location.replace("index.php");
				</script>
			<?
			exit;
		}
		else{	
			//같은 그룹의 소속인지 확인합니다. 먼저 내 그룹을 조회합니다.
			$t_id = $_SESSION['sess_username'];
			$T_SQL = "SELECT * FROM eval_user WHERE user_id LIKE '%$t_id%'";
			$tsql_query = mysqli_query($db_link, $T_SQL);
			while($trow = mysqli_fetch_array($tsql_query)) {
				$user_group = $trow[user_group];
				$user_team = $trow[user_team];
				$user_tema2 = $trow[user_team2];
			}
		
			// 대상자의 그룹을 조회합니다. 
			$SQL = "SELECT * FROM eval_user WHERE  user_id = '$_GET[uid]' and user_group LIKE '%$user_group%' ORDER BY user_use DESC,user_group asc";
			$tmp = $SQL;
			$sql_query = mysqli_query($db_link, $SQL);
			while($row = mysqli_fetch_array($sql_query)) {
				$user_group2 = $row[user_group];
			}

			if(strpos($user_group2 ,$user_group)!== false || strpos($user_group2, $user_team2)==false){ //대상자와 내 그룹이 일치&겸직그룹과 일치할때만 조회가능합니다.
				$user_id = $_GET[uid];
			}
			else {
				?>
				<script>
					location.replace("index.php");
				</script>
				<?
			exit;
			}
			
		}
	}

	// 인사정보
	$SQL = " SELECT * FROM eval_user u WHERE u.user_id = '$user_id'";
    $sql_query = mysqli_query($db_link, $SQL);
    while($row = mysqli_fetch_array($sql_query)) {

        $user_no = $row[user_no];
		$user_name = $row[user_name];
		$user_rank = $row[user_rank];
		$user_use = $row[user_use];
    }

	// 내 평가 정보
    $SQL = " SELECT * FROM tb_evaluation e LEFT JOIN eval_user u ON e.user_id = u.user_id WHERE u.user_id = '$user_id' ORDER BY length(YEAR), YEAR desc, half desc ";
    $sql_query = mysqli_query($db_link, $SQL);
    while($row = mysqli_fetch_array($sql_query)) {

        $user_no = $row[user_no];
		$user_name = $row[user_name];
		$user_rank = $row[user_rank];
		if($user_rank == 'G1'||$user_rank == 'G2'){
			$tbody_html_user .=  "<tr>
			<td style = \"text-align:center;\">$row[year]</td>
			<td style = \"text-align:center;\">$row[half]</td>
			<td style = \"text-align:center;\">$row[total_grade]</td>
			<td style = \"text-align:center;\">$row[score]</td>
			<td style = \"text-align:center;\">$row[etc]</td>
			<td style = \"text-align:center;\">$row[comment]</td>
			</tr>";

			$total_score += $row['score'];

		}
		else{
			$tbody_html_user .=  "<tr>
			<td style = \"text-align:center;\">$row[year]</td>
			<td style = \"text-align:center;\">$row[half]</td>
			<td style = \"text-align:center;\">$row[total_grade]</td>
			<td style = \"text-align:center;\">미산출</td>
			<td style = \"text-align:center;\">$row[etc]</td>
			<td style = \"text-align:center;\">$row[comment]</td>
			</tr>";

			$total_score = $user_rank."는 평가 점수를 산출하지 않습니다.";
		}
		
    }

?>
<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header">나의 인사평가</h3>
	</div>
	<div class = "e_mid">
		<div class = "body_info">
			사번 : <?=$user_no?> | 이름 : <?=$user_name?> | 직무등급 : <?=$user_rank?> | 점수합산 : <span style = "color:red;"><?=$total_score?></span>
		</div>
		<div class = "body_table">
			<table class = "tbl_score">
				<!-- <caption class="blind">회원 리스트</caption> -->
					<colgroup>
						<col style="width:10%;">
						<col style="width:10%;">
						<col style="width:10%;">
						<col style="width:10%;">
						<col style="width:20%;">
						<col style="width:40%;">
					</colgroup>
					<thead>
						<tr>
							<th style = "text-align:center;">년도</th>
							<th style = "text-align:center;">반기</th>
							<th style = "text-align:center;">종합평가</th>
							<th style = "text-align:center;">평가점수</th>
							<th style = "text-align:center;">비고</th>
							<th style = "text-align:center;">상사코멘트</th>
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
	.h3_header{font-size:25px; font-weight:600; color:#01324b; margin-bottom:30px;}
	.body_info{font-size:20px; margin-bottom:25px;}
	/* table {width:100%; border:1px solid black; border-collapse:collapse;}
	th, td{border:1px solid black; height:40px; padding-left:10px;} */
	table{width:100%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:40px;}
</style>