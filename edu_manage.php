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

	$sql = "SELECT count(*) AS educnt FROM tb_education WHERE tb_education.use = 1";
	$result = mysqli_query($db_link, $sql);
	if ($result) {
		$row = mysqli_fetch_assoc($result);
		$total_record = $row['educnt'];
	} else {
		echo "쿼리 실행 중 오류 발생: " . mysqli_error($db_link);
	}

	$list = 10;
	$block_cnt = 10; 
	$block_num = ceil($page / $block_cnt); 
	$block_start = (($block_num - 1) * $block_cnt) + 1; // 블록의 시작 번호  ex) 1,6,11 ...
	$block_end = $block_start + $block_cnt - 1; // 블록의 마지막 번호 ex) 5,10,15 ...
	
	$total_page = ceil($total_record / $list);

	if($block_end > $total_page){ 
		$block_end = $total_page; 
	}
	$total_block = ceil($total_page / $block_cnt);
	$page_start = ($page - 1) * $list;
	
	// 내 평가 정보
    $SQL = "SELECT * FROM tb_education WHERE tb_education.use = 1 ORDER BY edu_startdate DESC LIMIT $page_start, $list";
    $sql_query = mysqli_query($db_link, $SQL);
    while($row = mysqli_fetch_array($sql_query)) {
        $edu_stdate = $row["edu_startdate"];
		$edu_eddate = $row["edu_enddate"];
		$edu_name = $row["edu_name"];
		$edu_institution = $row["edu_institution"];
		$edu_type_n = $row["edu_type"];
		$edu_id = $row["edu_id"];

		if($edu_type_n == 3){
			$edu_type = "전사교육";
		} else if ($edu_type_n == 0){
			$edu_type = "임원교육";
		} else if ($edu_type_n == 1){
			$edu_type = "팀장교육";
		} else {
			$edu_type = "개별교육";
		}

		$tbody_html_user .=  "<tr>
								<td style = \"text-align:center;\">$edu_type</td>
								<td style = \"text-align:center;\"><a href = \"./education_edit.php?eid=$edu_id\">$edu_name</a></td>
								<td style = \"text-align:center;\">$edu_institution</td>
								<td style = \"text-align:center;\">$edu_stdate~$edu_eddate</td>
							</tr>";
    }

?>
<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header">교육과정 관리</h3>
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
							<th style = "text-align:center;">교육대상</th>
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
	<div class = "goadd">
		<a href = "./education_add.php" style = "color:white;"><button type = "button" class = "eduadd">교육추가</button></a>
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
					echo "<span style = \"font-weight:600; color:#01324b; border-bottom:1px solid black; margin:15px; font-size:20px;\">$i</span>";
				} else {
					echo "<a href='edu_manage.php?page=$i' style = \"margin:10px;\"> $i </a> ";
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
<style>
	.e_body{margin:40px;}
	.h3_header{font-size:25px; font-weight:600; color:#01324b; margin-bottom:30px;}
	.body_info{font-size:20px; margin-bottom:25px;}
	table{width:100%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:40px;}
	.goadd {margin-top:40px; text-align:center;}
	.eduadd {background-color:#214796; height:40px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px;}
</style>