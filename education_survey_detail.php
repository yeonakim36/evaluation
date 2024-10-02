<?php
    include "./gnb.php";

	if(!$_SESSION['sess_userid']) {
		?>
			<script>
				location.replace("index.php");
			</script>
		<?
		exit;
	}

	$edu_id = $_GET['eid'];
	$user_id = $_GET['uid'];
	$user_name = $_SESSION['sess_username'];

	$SQL = " SELECT * 
			FROM education_list el
			INNER JOIN education_report er
			ON el.user_id = er.user_id AND el.edu_id = er.edu_id
			WHERE el.edu_id = '$edu_id' AND el.user_id = '$user_id' ";

	$sql_query = mysqli_query($db_link, $SQL);
	while($row = mysqli_fetch_array($sql_query)) {
		$edu_id = $row["edu_id"];
		$edu_startdate = $row["edu_startdate"];
		$edu_enddate = $row["edu_enddate"];
		$edu_name = $row["edu_name"];
		$edu_institution = $row["edu_institution"];
		$edu_period = $row["edu_period"];
		$edu_time = $row["edu_time"];
		$edu_type = $row["edu_type"];
		$edu_file_yn = $row["edu_file_yn"];
		$edu_t1 = $row["edu_t1"];
		$edu_t2 = $row["edu_t2"];
		$edu_t3 = $row["edu_t3"];
		$edu_t4 = $row["edu_t4"];
		$edu_t5 = $row["edu_t5"];
		$file_path = $row["file_path"];
	}
	$myfiles = array_diff(scandir($file_path), array('.', '..')); 
	$count = count($myfiles);
?>
<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header">교육참여 만족도 조사 [완료]</h3>
	</div>
	<div class = "e_mid">
		<div class = "body_table">
			<table>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육과정명</span>
					</th>
					<td>
						<input type = "text" name = "edu_name" value = "<?= $edu_name ?>"class = "input_title" readonly> 
						<input type = "hidden" name = "edu_id" value = "<?= $edu_id ?>" class = "input_title" readonly>
						<input type = "hidden" name = "edu_file_yn" value = "<?= $edu_file_yn ?>" class = "input_title" readonly>
					</td>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육기관</span>
					</th>
					<td>
						<input type = "text" name = "edu_institution" value = "<?= $edu_institution ?>"class = "input_title" readonly>
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육일정</span></span>
					</th>
					<td colspan = "3">
						<input type = "text" name = "edu_startdate" id = "edu_startdate" value = "<?= $edu_startdate ?>" readonly> ~ <input type = "text" name = "edu_enddate" id = "edu_enddate" value = "<?= $edu_enddate ?>" readonly> 
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육일수</span>
					</th>
					<td>
						<input type = "text" name = "edu_period" value = "<?= $edu_period ?>"class = "input_title" readonly>
					</td>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육시간</span>
					</th>
					<td>
						<input type = "text" name = "edu_time" value = "<?= $edu_time ?>"class = "input_title" readonly>
					</td>
				</tr>
			</table>
		</div>
		<div class = "survey">
			<h5 class = "h5_header">[만족도]</h5>
			<div class = "h5_div">
				<span class = "h5_span">1. 교육에 대한 전반적인 반족도를 체크해주세요.<br>
					<label for = "et1_1" style = "margin-bottom:-5px;"><input type = "radio" id = "et1_1" name = "edu_t1" value = "0" <?php if($edu_t1 == 0) echo "checked"; ?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">매우만족</span></label>
					<label for = "et1_2" style = "margin-bottom:-5px;"><input type = "radio" id = "et1_2" name = "edu_t1" value = "1" <?php if($edu_t1 == 1) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">만족</span>
					<label for = "et1_3" style = "margin-bottom:-5px;"><input type = "radio" id = "et1_3" name = "edu_t1" value = "2" <?php if($edu_t1 == 2) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">보통</span>
					<label for = "et1_4" style = "margin-bottom:-5px;"><input type = "radio" id = "et1_4" name = "edu_t1" value = "3" <?php if($edu_t1 == 3) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">불만족</span>
					<label for = "et1_5" style = "margin-bottom:-5px;"><input type = "radio" id = "et1_5" name = "edu_t1" value = "4" <?php if($edu_t1 == 4) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">매우불만족</span>
				</span>
			</div>
			<h5 class = "h5_header">[이해도]</h5>
			<div class = "h5_div">
				<span class = "h5_span">2. 강의내용과 교안 등의 전반적인 이해수준을 체크해주세요.<br>
					<label for = "et2_1" style = "margin-bottom:-5px;"><input type = "radio" id = "et2_1" name = "edu_t2" value = "0" <?php if($edu_t2 == 0) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">아주쉬운</span></label>
					<label for = "et2_2" style = "margin-bottom:-5px;"><input type = "radio" id = "et2_2" name = "edu_t2" value = "1" <?php if($edu_t2 == 1) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">쉬운</span>
					<label for = "et2_3" style = "margin-bottom:-5px;"><input type = "radio" id = "et2_3" name = "edu_t2" value = "2" <?php if($edu_t2 == 2) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">보통</span>
					<label for = "et2_4" style = "margin-bottom:-5px;"><input type = "radio" id = "et2_4" name = "edu_t2" value = "3" <?php if($edu_t2 == 3) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">어려운</span>
					<label for = "et2_5" style = "margin-bottom:-5px;"><input type = "radio" id = "et2_5" name = "edu_t2" value = "4" <?php if($edu_t2 == 4) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">매우어려운</span>
				</span>
			</div>
			<h5 class = "h5_header">[업무적합성]</h5>
			<div class = "h5_div">
				<span class = "h5_span">3. 해당교육이 본인의 업무에 적합한 교육이었는지 체크해주세요.<br>
					<label for = "et3_1" style = "margin-bottom:-5px;"><input type = "radio" id = "et3_1" name = "edu_t3" value = "0" <?php if($edu_t3 == 0) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">아주적합</span></label>
					<label for = "et3_2" style = "margin-bottom:-5px;"><input type = "radio" id = "et3_2" name = "edu_t3" value = "1" <?php if($edu_t3 == 1) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">적합</span>
					<label for = "et3_3" style = "margin-bottom:-5px;"><input type = "radio" id = "et3_3" name = "edu_t3" value = "2" <?php if($edu_t3 == 2) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">보통</span>
					<label for = "et3_4" style = "margin-bottom:-5px;"><input type = "radio" id = "et3_4" name = "edu_t3" value = "3" <?php if($edu_t3 == 3) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">부적합</span>
					<label for = "et3_5" style = "margin-bottom:-5px;"><input type = "radio" id = "et3_5" name = "edu_t3" value = "4" <?php if($edu_t3 == 4) echo "checked";?> disabled /> <span style = "line-height:3;margin-right:20px;font-weight:500;">매우부적합</span>
				</span>
			</div>
			<h5 class = "h5_header">[교육소감]</h5>
			<div class = "h5_div">
				<span class = "h5_span">
					4. 교육을 통해서 느꼈던 유익한 점과 아쉬운 점을 각각 간략히 기술해주세요.<br><br>
					유익한 점 : <input type = "text" name = "edu_t4" class = "input_title" style = "width:1000;" value = "<?= $edu_t4 ?>" readonly><br><br>
					아쉬운 점 : <input type = "text" name = "edu_t5" class = "input_title" style = "width:1000;" value = "<?= $edu_t5 ?>" readonly><br>
				</span>
			</div>
		</div>
		<?php if ($edu_file_yn == 1){?>
			<h5 class = "h5_header">[첨부파일] (수료증 혹은 교육자료 첨부)</h5>
			<div class = "h5_div">
				<span class = "h5_span">
					<div id="file-list">
						<?
							$url = "http://".$_SERVER["HTTP_HOST"].$file_path;
							$f_name = basename($url);
							$f_real_name = str_replace("attach", "", $f_name);
							echo "<a href = ".$url." target = \"_blank;\">$f_real_name</a><br>";
						?>
					</div>
				</span>
			</div>
		<?php }?>
		<div class = "golist">
			<a href = "./myeducation.php" style = "color:white;"><button type = "button" class = "listbtn">목록</button></a>
		</div>
	</div>
</div>
</body>
</html>
<? include "./foot.php";?>
<style>
	.e_body{margin:40px;margin-bottom:100px;}
	.h3_header{font-size:25px; font-weight:600; color:#01324b; margin-bottom:30px;}
	.body_info{font-size:18px; margin-bottom:25px;}
	table{width:80%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:50px;}
	th{width:8%;}
	.golist {margin-top:130px; text-align:center;}
	.listbtn {background-color:#214796; height:40px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px;}
	.re_bold {font-weight:bold;}
	.re_td2 {margin-left:20px; margin-right:20px;}
	input[type="radio"] {display:inline-block;}
	.h5_header{font-size:20px; font-weight:600; color:#01324b; margin-bottom:20px; margin-top:30px;}
	.h5_span{font-size:16px;}
	input[type=file]::file-selector-button{color:#01324b; width:30%; font-size:14px; height:30px; font-weight:700; background: #fff; border: 1px solid #01324b;}
	#delete-files{color:#01324b; width:12%; font-size:14px; height:30px; font-weight:700; background: #fff; border: 1px solid #01324b;}
</style>