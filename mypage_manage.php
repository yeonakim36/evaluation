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
		$user_id = $_GET[uid];
	}

	// 인사정보
	$SQL = " SELECT * FROM eval_user u WHERE u.user_id = '$user_id'";
    $sql_query = mysqli_query($db_link, $SQL);
    while($row = mysqli_fetch_array($sql_query)) {
        $user_no = $row[user_no];
		$user_name = $row[user_name];
		$user_rank = $row[user_rank];
		$user_use = $row[user_use];
		$user_grade = $row[user_grade];
    }

	// 내 평가 정보
    $SQL = " SELECT * FROM tb_evaluation e LEFT JOIN eval_user u ON e.user_id = u.user_id WHERE u.user_id = '$user_id' ORDER BY length(YEAR), YEAR desc, half desc ";
    $sql_query = mysqli_query($db_link, $SQL);
    while($row = mysqli_fetch_array($sql_query)) {

        $user_no = $row[user_no];
		$user_name = $row[user_name];
		$user_rank = $row[user_rank];
		$s_id = $row[s_id];
		$user_use = $row[user_use];

	
		if($user_rank == 'G1'||$user_rank == 'G2'){
			$tbody_html_user .=  "<tr>
								<td style = \"text-align:center;\"><input type = \"hidden\" name = \"year\" value = \"$row[year]\" class = \"year\">$row[year]</td>
								<td style = \"text-align:center;\"><input type = \"hidden\" name = \"half\" value = \"$row[half]\" class = \"half\">$row[half]</td>
								<td style = \"text-align:center;\"><input type = \"text\" name = \"total_grade\" value = \"$row[total_grade]\" id = \"input_txt1\" class = \"total_grade\"></td>
								<td style = \"text-align:center;\"><input type = \"text\" name = \"score\" value = \"$row[score]\" id = \"input_txt1\" class = \"score\"></td>
								<td style = \"text-align:center;\"><input type = \"text\" name = \"etc\" value = \"$row[etc]\" id = \"input_txt1\" class = \"etc\"></td>
								<td style = \"text-align:center;\">$row[comment]</td>
								<input type = \"hidden\" name = \"s_id\" value = \"$s_id\">
							</tr>";

		$total_score += $row['score'];

		}
		else{
			$tbody_html_user .=  "<tr>
								<td style = \"text-align:center;\"><input type = \"hidden\" name = \"year\" value = \"$row[year]\" class = \"year\">$row[year]</td>
								<td style = \"text-align:center;\"><input type = \"hidden\" name = \"half\" value = \"$row[half]\" class = \"half\">$row[half]</td>
								<td style = \"text-align:center;\"><input type = \"text\" name = \"total_grade\" value = \"$row[total_grade]\" id = \"input_txt1\" class = \"total_grade\"></td>
								<td style = \"text-align:center;\"><input type = \"text\" name = \"score\" value = \"$row[score]\" id = \"input_txt1\" class = \"score\"></td>
								<td style = \"text-align:center;\"><input type = \"text\" name = \"etc\" value = \"$row[etc]\" id = \"input_txt1\" class = \"etc\"></td>
								<td style = \"text-align:center;\">$row[comment]</td>
								<input type = \"hidden\" name = \"s_id\" value = \"$s_id\">
							</tr>";

		$total_score = $user_rank."는 평가점수를 산출하지 않도록 합니다.";
		}
    }

?>
<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header">[관리자] 인사평가 관리</h3>
	</div>
	<div class = "head_btn">
		<button type = "button" class = "go_promotion" onclick = "goPromote();">승진</button>
		<button type = "submit" class = "go_save" onclick = "goSubmit();">SAVE</button>
	</div>
	<div class = "e_mid">
		<div class = "body_info">
			사번 : <input type = "text" name = "user_no" value = "<?=$user_no?>" class = "input_txt2"> | 
			이름 : <?=$user_name?> | 
권한등급 : <select id = "user_grade" name = "user_grade">
							<option name = "user_grade" value = "0" <?php if($user_grade == "0") echo "selected";?>>일반</option>
							<option name = "user_grade" value = "2" <?php if($user_grade == "2") echo "selected";?>>차상위평가자</option>
							<option name = "user_grade" value = "1" <?php if($user_grade == "1") echo "selected";?>>관리자</option>
						</select> |
			직무등급 : <select id = "user_rank" name = "user_rank">
							<option name = "user_rank" value = "G1" <?php if($user_rank == "G1") echo "selected";?>>G1</option>
							<option name = "user_rank" value = "G2" <?php if($user_rank == "G2") echo "selected";?>>G2</option>
							<option name = "user_rank" value = "G3" <?php if($user_rank == "G3") echo "selected";?>>G3</option>
							<option name = "user_rank" value = "E0" <?php if($user_rank == "E0") echo "selected";?>>E0</option>
							<option name = "user_rank" value = "E1" <?php if($user_rank == "E1") echo "selected";?>>E1</option>
							<option name = "user_rank" value = "E2" <?php if($user_rank == "E2") echo "selected";?>>E2</option>
							<option name = "user_rank" value = "임금피크" <?php if($user_rank == "임금피크") echo "selected";?>>임금피크</option>
						</select> |
			점수합산 : <span style = "color:red;"><?=$total_score?></span>
		</div>
		<div class = "body_table">
			<table class = "tbl_score" id = "evaluationTable">
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
		<div class = "bottom_btn">
			<?php if($user_use == 1){ ?>
				<button type = "button" class = "inactive" onclick = "goinactive();">사용자 비활성화</button>
			<?php } else { ?>
				<button type = "button" class = "active" onclick = "goactive();">사용자 활성화</button>
			<?php } ?>
		</div>
		<div class = "golist">
			<button type = "button" class = "listbtn" onclick = "history.back(-1)">목록</button>
		</div>
	</div>
</div>
</body>
</html>
<? include "./foot.php";?>
<script>
	function goSubmit() { //SAVE

		isSubmitClick = true;

		var fnc = "mypage_edit";
		var user_id = "<? echo $user_id;?>";
		var user_no = $("input[name='user_no']").val();
		var user_rank = $("select[name=user_rank]").val();
		var user_grade = $("select[name=user_grade]").val();

		if(user_no == ""){
			alert("사번을 입력해주세요.");
			$("input[name='user_no']").focus();
			return;
		}
		if(user_rank == ""){
			alert("직무등급을 입력해주세요.");
			$("input[name='user_rank']").focus();
			return;
		}

		var rows = $('#evaluationTable tbody tr');
		var completedRequests = 0;

		if(rows.length == 0){
			var year = $(this).find('.year').val();
			var half = $(this).find('.half').val();
			var total_grade = $(this).find('.total_grade').val();
			var score = $(this).find('.score').val();
			var etc = $(this).find('.etc').val();

			$.ajax({
				type: 'POST',
				url: 'mypage_edit_form.php',
				data: {
					"function" : fnc,
					year: year,
					half: half,
					total_grade: total_grade,
					score: score,
					"user_id" : user_id,
					"user_no" : user_no,
					"user_rank" : user_rank,
					"user_grade" : user_grade,
					"etc" : etc
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

		rows.each(function() { //table row 갯수대로 for문 돌려서 변경된 값 가져오기
                var year = $(this).find('.year').val();
                var half = $(this).find('.half').val();
                var total_grade = $(this).find('.total_grade').val();
                var score = $(this).find('.score').val();
				var etc = $(this).find('.etc').val();

                $.ajax({
                    type: 'POST',
                    url: 'mypage_edit_form.php',
                    data: {
						"function" : fnc,
                        year: year,
                        half: half,
                        total_grade: total_grade,
                        score: score,
						"user_id" : user_id,
						"user_no" : user_no,
						"user_rank": user_rank,
						"user_grade" : user_grade,
						"etc" : etc
                    },
                    success: function(response) {
						completedRequests++;

						if (completedRequests === rows.length) {
                            alert('모든 업데이트가 완료되었습니다.');
							location.reload(true);
                        }
                    },
                    error: function(error) {
                        console.log('Error: ' + error);
                    }
                });
            });
	}

	function goPromote() { //승진
		$('.etc').val('승진리셋');
		$('.score').val('0');
		alert('승진 변경되었습니다. 직무등급 변경후 SAVE눌러 완료 하세요');
	}

	function goinactive(){
		var fnc = "goinactive";
		var user_id = "<? echo $user_id;?>";

		$.ajax({
			type: 'POST',
			url: 'mypage_edit_form.php',
			data: {
				"function" : fnc,
				"user_id" : user_id
			},
			success: function(response) {
				alert('사용자가 비활성화되었습니다.');
				location.reload(true);
			},
			error: function(error) {
				console.log('Error: ' + error);
			}
		});
	}

	function goactive(){
		var fnc = "goactive";
		var user_id = "<? echo $user_id;?>";

		$.ajax({
			type: 'POST',
			url: 'mypage_edit_form.php',
			data: {
				"function" : fnc,
				"user_id" : user_id
			},
			success: function(response) {
				alert('사용자가 활성화되었습니다.');
				location.reload(true);
			},
			error: function(error) {
				console.log('Error: ' + error);
			}
		});
	}
</script>
<style>
	.e_body{margin:40px;margin-bottom:100px;}
	.h3_header{font-size:25px; font-weight:600; color:#01324b; margin-bottom:30px;}
	.head_btn{float:right;}
	.b_sum{float:right; margin:20px 0px; font-size:20px;}
	.body_info{font-size:18px; margin-bottom:25px;}
	/* table {width:100%; border:1px solid black; border-collapse:collapse;}
	th, td{border:1px solid black; height:40px; padding-left:10px;padding-right:10px;} */
	table{width:100%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:40px;}
	#input_txt1{width:90%;}
	.bottom_btn{float:right; margin-top:30px; margin-bottom:30px;}
	.inactive, .active {background-color:#214796; height:40px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px;}
	.go_promotion, .go_save {background-color:#214796; height:30px; color:white; border:none; border-radius:8px; padding-right:15px; padding-left:15px;}
	.golist {margin-top:130px; text-align:center;}
	.listbtn {background-color:#214796; height:40px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px;}
</style>