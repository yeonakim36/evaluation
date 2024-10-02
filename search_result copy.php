<?php
include "./gnb.php";

$search_option = $_GET['search_option'];
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
	$user_use = 2;
}

$sql = "";
$db_link = db_conn();

if ($search_option == "user_name") {
    $sql = "SELECT * FROM eval_user WHERE user_group LIKE '%abov%' AND user_name LIKE '%$search_query%'";
} else if ($search_option == "user_id") {
    $sql = "SELECT * FROM eval_user WHERE user_group LIKE '%abov%' AND user_id LIKE '%$search_query%'";
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

    if($row[user_use] == 1){
        $use_yn = "재직중";
    } else {
        $use_yn = "퇴사";
    }

    $tbody_html_user .=  "<tr>
                            <td><a href = \"/evaluation/mypage_manage.php?uid=$row[user_id]\">$row[user_id]</a></td>
                            <td>$row[user_name]</td>
                            <td>$row[user_no]</td>
                            <td>$row[user_rank]</td>
                            <td>$row[user_group]</td>
                            <td>$row[last_login]</td>
                            <td>$use_yn</td>
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
	<form method="get" action="search_result.php">
		<div class = "bottom_btn">
			<div style="width:10%;float:left;">
				<select name = "search_option" class="form-control">
					<option value = "user_id">ID</option>
					<option value = "user_name">이름</option>
				</select> 
			</div>
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
			<table class = "tbl_score">
				<!-- <caption class="blind">회원 리스트</caption> -->
					<colgroup>
						<col style="width:10%;">
						<col style="width:15%;">
						<col style="width:5%;">
						<col style="width:10%;">
						<col style="width:30%;">
						<col style="width:15%;">
						<col style="width:10%;">
					</colgroup>
					<thead style = "text-align:center;">
						<tr>
							<th>ID</th>
							<th>이름</th>
							<th>사번</th>
							<th>직무등급</th>
							<th>소속그룹</th>
							<th>마지막로그인</th>
							<th>사용여부</th>
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
<script>
	function gosync(){
		var fnc = "gosync";

		$.ajax({
			type: 'POST',
			url: 'adsync.php',
			data: {
				"function" : fnc
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
</script>
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
</style>