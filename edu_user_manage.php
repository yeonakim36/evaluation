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
if($_SESSION['sess_manage'] != 1) { //관리자 권한확인
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
$sessionid = $_SESSION['sess_userid'];
$sessiongrade = $_SESSION['sess_grade'];
$search_query = $_GET['search_query'];

if(isset($_GET['user_use1'])){
	if(isset($_GET['user_use0'])){
		$user_use = 2;
	} else {
		$user_use = 1;
	}
} else if(isset($_GET['user_use0'])){
	if(isset($_GET['user_use1'])){
		$user_use = 2;
	} else {
		$user_use = 0;
	}
} else {
	$user_use = 1;
}

$sql = "";
$db_link = db_conn();

if ($search_option == "user_name") {
    $sql = "SELECT * FROM eval_user WHERE user_group LIKE '%abov%' AND user_name LIKE '%$search_query%'";
} else if ($search_option == "user_id") {
    $sql = "SELECT * FROM eval_user WHERE user_group LIKE '%abov%' AND user_id LIKE '%$search_query%'";
} else{
	$sql = "SELECT * FROM eval_user WHERE user_group LIKE '%abov%' AND (user_id LIKE '%$search_query%' OR user_name LIKE '%$search_query%')";
}

if ($user_use == 1) {
	$sql .= " AND user_use = 1 ORDER BY user_group asc";
} else if ($user_use == 0) {
	$sql .= " AND user_use = 0 ORDER BY user_group asc";
} else {
	$sql .= " AND (user_use = 0 OR user_use = 1) ORDER BY user_use DESC, user_group asc";
}

$result = mysqli_query($db_link, $sql);
while($row = mysqli_fetch_array($result)) {

    if($row['user_use'] == 1){
        $use_yn = "재직중";
    } else {
        $use_yn = "퇴사";
    }

	$user_level = $row["user_level"];
	if($user_level == 0) {
		$str1 = "<option name = \"user_level\" value = \"0\" selected>임원</option>
				<option name = \"user_level\" value = \"1\">팀장</option>
				<option name = \"user_level\" value = \"2\">사원</option>";
	} else if ($user_level == 1) {
		$str1 = "<option name = \"user_level\" value = \"0\">임원</option>
				<option name = \"user_level\" value = \"1\" selected>팀장</option>
				<option name = \"user_level\" value = \"2\">사원</option>";
	} else {
		$str1 = "<option name = \"user_level\" value = \"0\">임원</option>
				<option name = \"user_level\" value = \"1\">팀장</option>
				<option name = \"user_level\" value = \"2\" selected>사원</option>";
	}

	$user_manage = $row["user_manage"];
	if($user_manage == 0) {
		$str2 = "<option name = \"user_manage\" value = \"0\" selected>일반</option>
				<option name = \"user_manage\" value = \"1\">관리자</option>";
	} else {
		$str2 = "<option name = \"user_manage\" value = \"0\">일반</option>
				<option name = \"user_manage\" value = \"1\" selected>관리자</option>";
	}
    $tbody_html_user .=  "<tr>
                            <td>$row[user_id]</td>
                            <td>$row[user_name]</td>
                            <td>$row[user_group]</td>
                            <td>
								<select id = \"u_level_$row[user_id]\" name = \"user_level\">
									$str1
								</select>
							</td>							
							<td>
								<select id = \"u_manage_$row[user_id]\" name = \"user_manage\">
									$str2
								</select>
							</td>
                            <td>$use_yn</td>
                            <td><button type=\"button\" class=\"go_submit\" onclick='submit_popup(\"$row[user_id]\", document.getElementById(\"u_level_$row[user_id]\").value, document.getElementById(\"u_manage_$row[user_id]\").value);'>저장</button></td>
                        </tr>";
}

// 데이터베이스 연결 닫기
$db_link->close();
    
?>
<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header">사원 관리</h3>
	</div>
	<form method="get" action="edu_user_manage.php">
		<div class = "bottom_btn">
			<div style="margin-left:15px;float:left;">
				<input type="text" style="width:330px;" class="form-control" name="search_query" value = "<?php echo $search_query;?>">
			</div>
			<div style="margin-left:15px;float:left;font-size:16px;line-height:35px;">
				<input type = "checkbox" name = "user_use1" id = "current" value = "1" style = "margin:4px 5px 0;"<?php if($user_use == "1" || $user_use == "2") echo "checked";?>><label for = "current" style = "font-weight:400">재직자</label>
				<input type = "checkbox" name = "user_use0" id = "former" value = "0" style = "margin:4px 5px 0;"<?php if($user_use == "0" || $user_use == "2") echo "checked";?>><label for = "former" style = "font-weight:400">퇴사자</label>
			</div>
			<div style="margin-left:10px;width:15%;float:left;">
				<button class="btn btn-light" type="submit">
					<i class="fa fa-exchange"></i> Search
				</button>
			</div>
			<button type = "button" class = "sync" onclick = "gosync();">AD 동기화</button>
		</div>
	</form>
	<div class = "e_mid">
		<div class = "body_table">
			<table class = "tbl_score" id = "userTable">
				<colgroup>
						<col style="width:10%;">
						<col style="width:15%;">
						<col style="width:25%;">
						<col style="width:10%;">
						<col style="width:10%;">
						<col style="width:10%;">
						<col style="width:10%;">
					</colgroup>
					<thead>
						<tr>
							<th style = "text-align:left;">ID</th>
							<th style = "text-align:left;">이름</th>
							<th style = "text-align:left;">소속그룹</th>
							<th style = "text-align:left;">직급</th>
							<th style = "text-align:left;">권한</th>
							<th style = "text-align:left;">사용여부</th>
							<th style = "text-align:left;">버튼</th>
						</rddtr>
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
<script>
	function gosync(){
		var fnc = "gosync";
		var sessionid = "<?=$sessionid?>";
		var sessiongrade = "<?=$sessiongrade?>";

		$.ajax({
			type: 'POST',
			url: 'adsync.php',
			data: {
				"function" : fnc, "sessionid" : sessionid, "sessiongrade" : sessiongrade
			},
			success: function(response) {
				alert('AD와 동기화되었습니다.');
				location.reload(true);
			},
			error: function(error) {
				console.log('Error: ' + error);
			}
		});
	}
	function submit_popup(user_id, user_level, user_manage){
		isSubmitClick = true;
		var fnc = "change_level";
		
		$.ajax({
			type: 'POST',
			url: 'edu_manage_form.php',
			data: {
				"function" : fnc,
				"user_id" : user_id,
				"user_level" : user_level,
				"user_manage" : user_manage
			},
			success: function(response) {
				alert('모든 업데이트가 완료되었습니다.');
				location.reload(true);
			},
			error: function(error) {
				console.log('Error: ' + error);
			}
		});
	}
</script>
<style>
	.e_body{margin:40px;}
	.h3_header{font-size:22px; font-weight:600; color:#01324b;}
	.b_sum{float:right; margin:20px 0px;}
	table{width:100%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:40px;}
	.sync{background-color:#214796; height:35px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px; float:right; margin-bottom:15px;}
</style>