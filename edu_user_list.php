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

	$sql = "SELECT COUNT(*) as total FROM education_list el  LEFT JOIN eval_user eu ON el.user_id = eu.user_id WHERE el.`use` = 1";
	$result = mysqli_query($db_link, $sql);
	if ($result) {
		$row = mysqli_fetch_assoc($result);
		$total_record = $row['total'];
	} else {
		echo "쿼리 실행 중 오류 발생: " . mysqli_error($connection);
	}

	$list = 10;
	$block_cnt = 10; 
	$block_num = ceil($page / $block_cnt); 
	$block_start = (($block_num - 1) * $block_cnt) + 1;
	$block_end = $block_start + $block_cnt - 1;
	
	$total_page = ceil($total_record / $list);

	if($block_end > $total_page){ 
		$block_end = $total_page; 
	}
	$total_block = ceil($total_page / $block_cnt);
	$page_start = ($page - 1) * $list;
	
    $SQL = "SELECT  el.*, eu.user_name AS eu_user_name, el.user_name AS el_user_name, eu.user_group, eu.user_team 
			FROM education_list el left JOIN eval_user eu 
			ON el.user_id = eu.user_id 
			WHERE el.`use` = 1
			ORDER BY edu_type desc, result desc
			LIMIT $page_start, $list";
    $sql_query = mysqli_query($db_link, $SQL);
    while($row = mysqli_fetch_array($sql_query)) {
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
$edu_type = $row["edu_type"];
		
		if($result == 0){
			$rst_txt = "미수료";
			if($edu_type == 3){
				$rst_txt = "전사교육";
			} else if ($edu_type == 0){
				$rst_txt = "임원교육";
			} else if ($edu_type == 1){
				$rst_txt = "팀장교육";
			} 
			$tbody_html_user .=  "<tr>
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
			$tbody_html_user .=  "<tr style = 'cursor:pointer;' onclick = \"location.href='./education_survey_detail.php?eid=$edu_id&uid=$user_id'\">
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

?>
<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header">교육활동 관리</h3>
	</div>
	<button type = "button" class = "listdown" onclick = "listdown();">교육활동List 다운로드</button>
	<div class = "e_mid">
		<div class = "body_table">
			<table class = "tbl_score">
				<colgroup>
					<col style="width:11%;">
					<col style="width:10%;">
					<col style="width:20%;">
					<col style="width:15%;">
					<col style="width:11%;">
					<col style="width:11%;">
					<col style="width:7%;">
					<col style="width:7%;">
					<col style="width:7%;">
				</colgroup>
				<thead>
					<tr>
						<th style = "text-align:center;">교육대상</th>
						<th style = "text-align:center;">이름</th>
						<th style = "text-align:center;">소속그룹</th>
						<th style = "text-align:center;">교육일자</th>
						<th style = "text-align:center;">교육과정</th>
						<th style = "text-align:center;">교육기관</th>
						<th style = "text-align:center;">수료여부</th>
						<th style = "text-align:center;">교육일수</th>
						<th style = "text-align:center;">교육시간</th>
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
		if ($page > 1) {
			echo "<a href='?page=1'>&laquo; 처음</a> ";
		}
		if ($page > 1) {
			$prev_page = $page - 1;
			echo "<a href='?page=$prev_page'>&lsaquo; 이전</a> ";
		}
			for($i = $block_start; $i <= $block_end; $i++){
				if($page == $i){
					// echo "<b>$i</b>";
					echo "<span style = \"font-weight:600; color:#01324b; border-bottom:1px solid black; margin:15px; font-size:20px;\">$i</span>";
				} else {
					echo "<a href='edu_user_list.php?page=$i' style = \"margin:10px;\"> $i </a> ";
				}
			}
		if ($page < $total_page) {
			$next_page = $page + 1;
			echo "<a href='?page=$next_page'>다음 &rsaquo;</a> ";
		}
		if ($page < $total_page) {
			echo "<a href='?page=$total_page'>마지막 &raquo;</a>";
		}
	?>
</div>
<br>
</body>
</html>
<? include "./foot.php";?>
<script>
	function listdown(){
		location.href="./edu_list_download.php";
	}
</script>
<style>
	.e_body{margin:40px;}
	.h3_header{font-size:25px; font-weight:600; color:#01324b;}
	.body_info{font-size:20px; margin-bottom:25px;}
	table{width:100%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:40px;}
	.listdown{background-color:#214796; height:35px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px; float:right; margin-bottom:15px;margin-right:5px;}
</style>