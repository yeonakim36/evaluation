<?php
    include "./gnb.php";

	if(isset($_GET['page'])){
		$page = $_GET['page'];
	} else {
		$page = 1;
	}

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
		$user_id = $_GET[uid];
	}

	$sql = "SELECT COUNT(*) as  total
			FROM education_list el
			left JOIN eval_user eu
			ON el.user_id = eu.user_id
			left JOIN tb_education te
			ON el.edu_id = te.edu_id
			WHERE el.user_id = '$user_id' 
			AND el.use = 1";
	$result = mysqli_query($db_link, $sql);
	if ($result) {
		$row = mysqli_fetch_assoc($result);
		$total_record = $row['total'];
	} else {
		echo "쿼리 실행 중 오류 발생: " . mysqli_error($connection);
	}
	$list = 10;
	$block_cnt = 5; 
	$block_num = ceil($page / $block_cnt); 
	$block_start = (($block_num - 1) * $block_cnt) + 1;
	$block_end = $block_start + $block_cnt - 1;
	
	$total_page = ceil($total_record / $list);

	if($block_end > $total_page){ 
		$block_end = $total_page; 
	}
	$total_block = ceil($total_page / $block_cnt);
	$page_start = ($page - 1) * $list;

	$SQL = "SELECT * 
		FROM education_list el
		left JOIN eval_user eu
		ON el.user_id = eu.user_id
		left JOIN tb_education te
		ON el.edu_id = te.edu_id
		WHERE el.user_id = '$user_id' 
		AND el.use = 1
					ORDER BY te.regist_time desc
		LIMIT $page_start, $list";
	$sql_query = mysqli_query($db_link, $SQL);
	while($row = mysqli_fetch_array($sql_query)) {
		$edu_stdate = $row["edu_startdate"];
		$edu_eddate = $row["edu_enddate"];
		$edu_name = $row["edu_name"];
		$edu_id = $row["edu_id"];
		$edu_institution = $row["edu_institution"];
		$edu_type_n = $row["edu_type"];
		$result = $row["result"];

		if($edu_type_n == 3){
			$edu_type = "전사교육";
			$tbody_html_user .= "<tr>
									<td style = \"text-align:center;\">$edu_type</td>
									<td style = \"text-align:center;\">$edu_name</td>
									<td style = \"text-align:center;\">$edu_institution</td>
									<td style = \"text-align:center;\">$edu_stdate~$edu_eddate</td>
								</tr>";
		} else if ($edu_type_n == 0){
			$edu_type = "임원교육";
			$tbody_html_user .= "<tr>
									<td style = \"text-align:center;\">$edu_type</td>
									<td style = \"text-align:center;\">$edu_name</td>
									<td style = \"text-align:center;\">$edu_institution</td>
									<td style = \"text-align:center;\">$edu_stdate~$edu_eddate</td>
								</tr>";
		} else if ($edu_type_n == 1){
			$edu_type = "팀장교육";
			$tbody_html_user .= "<tr>
									<td style = \"text-align:center;\">$edu_type</td>
									<td style = \"text-align:center;\">$edu_name</td>
									<td style = \"text-align:center;\">$edu_institution</td>
									<td style = \"text-align:center;\">$edu_stdate~$edu_eddate</td>
								</tr>";
		} else {
			$edu_type = "개별교육";
			if($result == 1){
				$tbody_html_user .= "<tr style = 'cursor:pointer;' onclick = \"location.href='./education_survey_detail.php?eid=$edu_id&uid=$user_id'\">
										<td style = \"text-align:center;\">$edu_type</td>
										<td style = \"text-align:center;\">$edu_name [설문완료]</td>
										<td style = \"text-align:center;\">$edu_institution</td>
										<td style = \"text-align:center;\">$edu_stdate~$edu_eddate</td>
									</tr>";
			} else {
				$tbody_html_user .= "<tr style = 'cursor:pointer;' onclick = \"location.href='./education_survey.php?eid=$edu_id'\">
										<td style = \"text-align:center;\">$edu_type</td>
										<td style = \"text-align:center;\"><a href = \"./education_survey.php?eid=$edu_id\">$edu_name [설문하기]</a></td>
										<td style = \"text-align:center;\">$edu_institution</td>
										<td style = \"text-align:center;\">$edu_stdate~$edu_eddate</td>
									</tr>";
			}
		}
	}

?>
<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header"><?=$user_id?>님의 교육이력</h3>
	</div>
	<div class = "e_mid">
		<div class = "body_table">
			<table class = "tbl_score">
				<colgroup>
					<col style="width:25%;">
					<col style="width:25%;">
					<col style="width:25%;">
					<col style="width:25%;">
				</colgroup>
				<thead>
					<tr>
						<th style = "text-align:center;">교육분류</th>
						<th style = "text-align:center;">교육과정명</th>
						<th style = "text-align:center;">교육기관</th>
						<th style = "text-align:center;">교육일정</th>
					</tr>
				</thead>
				<tbody>
					<?=$tbody_html_user?>
				</tbody>
			</table>
		</div>
	</div>
	
</div>
<div id = "page_num" style = "text-align:center;">
	<?php
		for($i = $block_start; $i <= $block_end; $i++){
			if($page == $i){
				echo "<span style = \"font-weight:600; color:#01324b; border-bottom:1px solid black; margin:15px; font-size:20px;\">$i</span>";
			} else {
				echo "<a href='job_board.php?page=$i' style = \"margin:10px;\"> $i </a> ";
			}
		}
	?>
</div>
</body>
</html>
<? include "./foot.php";?>
<style>
	.e_body{margin:40px;}
	.h3_header{font-size:25px; font-weight:600; color:#01324b; margin-bottom:30px;}
	.body_info{font-size:20px; margin-bottom:25px;}
	table{width:100%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:40px;}
	#page_num{margin-bottom : 30px;}
</style>