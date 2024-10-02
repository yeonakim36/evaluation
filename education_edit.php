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

	$edu_id = $_GET['eid'];

	function generateRandomCode($length = 6) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomCode = '';
		
		for ($i = 0; $i < $length; $i++) {
			$randomCode .= $characters[rand(0, strlen($characters) - 1)];
		}
		
		return $randomCode;
	}
	$code = generateRandomCode();

	$SQL = " SELECT * FROM tb_education WHERE edu_id = '$edu_id' ";
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
		$tot_user_name = $row["tot_user_name"];
	}
	$existing_users = array_map('trim', explode(',', $tot_user_name));
?>
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@1.1.0/dist/css/bootstrap-multiselect.min.css" rel="stylesheet">

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-multiselect@1.1.0/dist/js/bootstrap-multiselect.min.js"></script>

<!DOCTYPE html>
<html>
<body>
<div class = "e_body">
	<div class = "e_header">
		<h3 class = "h3_header">교육과정 수정</h3>
	</div>
	<div class = "e_mid">
		<div class = "body_table">
			<table class = "tbl_score" id = "edu_table">
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육구분</span></span>
					</th>
					<td>
						<span class = "re_td2" style = "margin-left:0px;">
							<label for = "et1" style = "margin-bottom:-5px;"><input type = "radio" id = "et1" name = "edu_type" value = "3" <?php if($edu_type == 3) echo "checked";?>/> <span style = "line-height:3;margin-right:20px;">전사</span></label>
							<label for = "et2" style = "margin-bottom:-5px;"><input type = "radio" id = "et2" name = "edu_type" value = "0" <?php if($edu_type == 0) echo "checked";?>/> <span style = "line-height:3;margin-right:20px;">임원</span>
							<label for = "et3" style = "margin-bottom:-5px;"><input type = "radio" id = "et3" name = "edu_type" value = "1" <?php if($edu_type == 1) echo "checked";?>/> <span style = "line-height:3;margin-right:20px;">팀장</span>
							<label for = "et4" style = "margin-bottom:-5px;"><input type = "radio" id = "et4" name = "edu_type" value = "2" <?php if($edu_type == 2) echo "checked";?>/> <span style = "line-height:3;margin-right:20px;">개별</span>
						</span>
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육과정명</span>
					</th>
					<td>
						<input type = "text" name = "edu_name" value = "<?= $edu_name ?>"class = "input_title">
						<input type = "hidden" name = "edu_id" value = "<?= $edu_id ?>" class = "input_title">
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육일수</span>
					</th>
					<td>
						<input type = "text" name = "edu_period" value = "<?= $edu_period ?>" class = "input_title">
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육시작일자</span></span>
					</th>
					<td>
						<input type = "text" name = "edu_startdate" id = "edu_startdate" value = "<?= $edu_startdate ?>"> 
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육종료일자</span></span>
					</th>
					<td>
						<input type = "text" name = "edu_enddate" id = "edu_enddate" value = "<?= $edu_enddate ?>"> 
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육시간</span>
					</th>
					<td>
						<input type = "text" name = "edu_time" value = "<?= $edu_time ?>"class = "input_title">
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">교육기관</span>
					</th>
					<td>
						<input type = "text" name = "edu_institution" value = "<?= $edu_institution ?>"class = "input_title">
					</td>
				</tr>
				<tr>
					<th>
						<span class = "re_td2"><span class = "re_bold">수료증첨부필수여부</span></span>
					</th>
					<td>
						<span class = "re_td2" style = "margin-left:0px;">
							<label for = "fileyn" style = "margin-bottom:-5px;"><input type = "checkbox" id = "fileyn" name = "edu_file_yn" value = "1" <?php if($edu_file_yn == 1) echo "checked";?>/> <span style = "line-height:3;margin-right:20px;">필수</span></label>
						</span>
					</td>
				</tr>
				<?
					$SQL = "SELECT  user_no, user_id, user_name FROM eval_user WHERE user_use = 1 order by user_group desc";
					$sql_query = mysqli_query($db_link, $SQL);
					while($row = mysqli_fetch_array($sql_query)) {
						$user_no = $row["user_no"];
						$user_id = $row["user_id"];
						$user_name = $row["user_name"];
						
						if(in_array($user_id, $existing_users)) {
							$tbody_html_user .= "<option value=\"$user_id\" selected>$user_name</option>";
						} else {
							$tbody_html_user .= "<option value=\"$user_id\">$user_name</option>";
						}
					}
				?>
				<tr id="selectpep" style="display: <?php echo ($edu_type == 2) ? 'table-row' : 'none'; ?>">
				<th>
					<span class = "re_td2"><span class = "re_bold">대상인원</span></span>
				</th>
				<td>
					<div class = "selectpeople">
						<select id="chkveg" multiple="multiple">
							<?= $tbody_html_user?>
						</select>
						<input type="button" id="btnget" value="확인"/>
					</div>
					<div id="selectedValues">
						<?php if(!empty($tot_user_name)) echo $tot_user_name;?>
					</div>
				</td>
			</tr>
			</table>
		</div>
		<div class = "golist">
			<button type = "button" class = "listbtn" onclick = "goSubmit()">저장</button>
			<a href = "./edu_manage.php" style = "color:white;"><button type = "button" class = "listbtn">목록</button></a>
			<button type = "button" class = "listbtn" onclick = "board_hide()">삭제</button>
		</div>
	</div>
</div>
</body>
</html>
<? include "./foot.php";?>
<script>
	$(document).ready(function() {
		$("#edu_startdate").flatpickr({
			// enableTime: true,
			// dateFormat: "Y-m-d H:i",
		});
		$("#edu_enddate").flatpickr({
			// enableTime: true,
			// dateFormat: "Y-m-d",
		});
    $('.multiselect-selected-text').text('대상자 선택');
		$('#chkveg').multiselect();
    });

	$('#et4').click(function() {
		console.log('dd');
		$('#selectpep').show();
	});

	$('#et1, #et2, #et3').click(function() {
        $('#chkveg').multiselect('deselectAll', false);
        $('#chkveg').multiselect('refresh');
		$('#selectedValues').text('');
		$('#selectpep').hide();
	});

	$('#chkveg').multiselect({
		includeSelectAllOption: true,
		enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
		filterBehavior: 'text', //text값으로 검색
		//filterBehavior: 'value' -> value값으로 검색

		filterFunction: function(element, query) {
			var value = $(element).text().toLowerCase();
			query = query.toLowerCase();
			return value.indexOf(query) >= 0;
		}
	});

	$('#btnget').click(function() {
		// var val = $('#chkveg').val();
		// var selectedValues = val.join(', ');
		var selectedTexts = [];
		$("#chkveg option:selected").each(function() {
			selectedTexts.push($(this).text());
		});
		var selectedValues = selectedTexts.join(', ');
    	$('#selectedValues').text(selectedValues);
	});

	function goSubmit() {
		isSubmitClick = true;
		
		var edu_name = $("input[name='edu_name']").val();
		var edu_id = $("input[name='edu_id']").val();
		var edu_period = $("input[name='edu_period']").val();
		var edu_startdate = $("input[name='edu_startdate']").val();
		var edu_enddate = $("input[name='edu_enddate']").val();
		var edu_time = $("input[name='edu_time']").val();
		var edu_institution = $("input[name='edu_institution']").val();
		var edu_file_yn = $("input[name='edu_file_yn']:checked").val();
		var edu_type = $("input[name='edu_type']:checked").val();
		
		if(edu_file_yn != 1){ //수료증첨부필수:1, 미체크:0
			edu_file_yn = 0;
		}

		//조건 추가하기
		if(edu_name == ""){
			alert("교육과정명을 입력해주세요.");
			$("input[name='edu_name']").focus();
			return;
		}
		if(edu_period == ""){
			alert("교육일수를 입력해주세요.");
			$("input[name='edu_period']").focus();
			return;
		}
		if(edu_startdate == ""){
			alert("교육 시작일자를 입력해주세요.");
			$("input[name='edu_startdate']").focus();
			return;
		}
		if(edu_enddate == ""){
			alert("교육 종료일자를 입력해주세요.");
			$("input[name='edu_enddate']").focus();
			return;
		}
		if(edu_time == ""){
			alert("교육시간을 입력해주세요.");
			$("input[name='edu_time']").focus();
			return;
		}
		if(edu_institution == ""){
			alert("교육기관을 입력해주세요.");
			$("input[name='edu_institution']").focus();
			return;
		}

		
		if(edu_type != 2){ //개별교육 아닌경우
			var fnc = "education_edit_all";
			$.ajax({
				type: 'POST',
				url: 'edu_manage_form.php',
				data: {
					"function" : fnc,
					"edu_name" : edu_name,
					"edu_id" : edu_id,
					"edu_period" : edu_period,
					"edu_startdate" : edu_startdate,
					"edu_enddate" : edu_enddate,
					"edu_time" : edu_time,
					"edu_institution" : edu_institution,
					"edu_file_yn" : edu_file_yn,
					"edu_type" : edu_type
				},
				dataType: 'json',
				success: function(response) {
					// console.log(response.status);
					if (response.status == 'success') {
						alert('모든 업데이트가 완료되었습니다.');
						location.reload(true);
					} else {
						alert('오류가 발생했습니다: ' + response.msg);
						console.log('Error: ' + response.msg);
					}
				},
				error: function(xhr, status, error) {
					alert('AJAX 오류입니다. 관리자에게 문의 바랍니다.');
					console.log('AJAX Error: ' + status + ' - ' + error);
				}
			});
		} else { //개별교육인경우
			var fnc = "education_edit";
			var userNameInputs = $('#chkveg').val()
			var tot_user_name = userNameInputs.toString();

			$.ajax({
				type: 'POST',
				url: 'edu_manage_form.php',
				data: {
					"function" : fnc,
					"edu_name" : edu_name,
					"tot_user_name" : tot_user_name,
					"edu_id" : edu_id,
					"edu_period" : edu_period,
					"edu_startdate" : edu_startdate,
					"edu_enddate" : edu_enddate,
					"edu_time" : edu_time,
					"edu_institution" : edu_institution,
					"edu_file_yn" : edu_file_yn,
					"edu_type" : edu_type
				},
				dataType: 'json',
				success: function(response) {
					if (response.status == 'success') {
						alert('모든 업데이트가 완료되었습니다.');
						location.reload(true);
					} else {
						alert('오류가 발생했습니다: ' + response.msg);
						console.log('Error: ' + response.msg);
					}
				},
				error: function(xhr, status, error) {
					alert('AJAX 오류입니다. 관리자에게 문의 바랍니다.');
					console.log('AJAX Error: ' + status + ' - ' + error);
				}
			});
		}
	}
			
	function board_hide(){
		var fnc = "board_hide";
		var edu_id = $("input[name='edu_id']").val();

		if(confirm("해당 교육 삭제하시겠습니까?")){
			jQuery.ajax(
				{ type: "POST"
					, url: "edu_manage_form.php"
					, data: {"function": fnc, "edu_id" : edu_id}
					, async : false
					, dataType : "json"
					, success: function(response) {
						if (response.status == 'success') {
							alert('모든 업데이트가 완료되었습니다.');
							window.location.href = './edu_manage.php';
						} else {
							alert('오류가 발생했습니다: ' + response.msg);
							console.log('Error: ' + response.msg);
						}
					},error: function (xhr, status, error) {
						alert('AJAX 오류입니다. 관리자에게 문의 바랍니다.');
						console.log('AJAX Error: ' + status + ' - ' + error);
					}
				});
		} else {
			return false;
		}
	}
</script>
<style>
	.e_body{margin:40px;margin-bottom:100px;}
	.h3_header{font-size:25px; font-weight:600; color:#01324b; margin-bottom:30px;}
	.body_info{font-size:18px; margin-bottom:25px;}
	table{width:100%;}
	table, td, th {border-bottom:1px solid #c0bebe; border-top:1px solid black; border-collapse:collapse; font-size:18px;}
	th, td{height:50px;}
	th{width:25%;}
	.golist {margin-top:130px; text-align:center;font-size:14px;}
	.listbtn, #btnget,.btn-group {background-color:#214796; height:40px; color:white; border:none; border-radius:8px; padding-right:20px; padding-left:20px;}
	.re_bold {font-weight:bold;}
	.re_td2 {margin-left:20px; margin-right:20px;}
	input[type="radio"] {display:inline-block;}
	.multiselect-container>li>a>label {padding: 4px 20px 3px 20px;}
	.multiselect-container{height:250px; overflow:scroll;font-size:11px;}
	.multiselect.dropdown-toggle.custom-select.text-center{background-color:#214796; border:none;}
	.multiselect-search.form-control{font-size:11px;}
</style>