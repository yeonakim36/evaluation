<?php
    include_once "./conndb.php";
	header("Content-Type:application/json");
	if (!function_exists('education_add_all')) { //개별교육 아닌 전사/임원/팀장 교육
		function education_add_all() {
			$msg = "success";
			$edu_name = $_POST['edu_name'];
			$edu_id = $_POST['edu_id'];
			$edu_period = $_POST['edu_period'];
			$edu_startdate = $_POST['edu_startdate'];
			$edu_enddate = $_POST['edu_enddate'];
			$edu_time = $_POST['edu_time'];
			$edu_institution = $_POST['edu_institution'];
			$edu_file_yn = $_POST['edu_file_yn'];
			$edu_type = $_POST['edu_type'];

			$db_link = db_conn();

			if($edu_type == 3){ //전사
				$sql0 = "SELECT user_id, user_name FROM eval_user";
				$result = mysqli_query($db_link, $sql0);
				if ($result) {
					$user_ids = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$user_id = $row['user_id'];
						$user_name = $row['user_name'];
						$user_ids[] = $row['user_id'];
						$sql1 = "INSERT INTO education_list (user_id, user_name, edu_id, edu_institution, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn) 
								VALUES ('$user_id', '$user_name', '$edu_id', '$edu_institution', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn);";
						$update_edu = mysqli_query($db_link, $sql1);
					}
					$total_id = implode(',', $user_ids);
				}
			} else if($edu_type == 0) { //임원
				$sql0 = "SELECT user_id, user_name FROM eval_user WHERE user_level = 0";
				$result = mysqli_query($db_link, $sql0);
				if ($result) {
					$user_ids = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$user_id = $row['user_id'];
						$user_name = $row['user_name'];
						$sql1 = "INSERT INTO education_list (user_id, user_name, edu_id, edu_institution, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn) 
								VALUES ('$user_id', '$user_name', '$edu_id', '$edu_institution', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn);";
						$update_edu = mysqli_query($db_link, $sql1);
						$user_ids[] = $row['user_id'];
					}
					$total_id = implode(',', $user_ids);
				}
			} else if($edu_type == 1) { //팀장
				$sql0 = "SELECT user_id, user_name FROM eval_user WHERE user_level = 1";
				$result = mysqli_query($db_link, $sql0);
				if ($result) {
					$user_ids = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$user_id = $row['user_id'];
						$user_name = $row['user_name'];
						$user_ids[] = $row['user_id'];
						$sql1 = "INSERT INTO education_list (user_id, user_name, edu_id, edu_institution, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn) 
								VALUES ('$user_id', '$user_name', '$edu_id', '$edu_institution', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn);";
						$update_edu = mysqli_query($db_link, $sql1);
					}
					$total_id = implode(',', $user_ids);
				}
			}

			if(!$update_edu){
				$msg = "update_error".mysqli_error($db_link);
				mysqli_rollback($db_link);
			} else {
				$sql2 = "INSERT IGNORE INTO tb_education (edu_institution, edu_id, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn, tot_user_name) VALUES ('$edu_institution', '$edu_id', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn, '$total_id');";
				$update_edu2 = mysqli_query($db_link, $sql2);
	
				if(!$update_edu2){
					$msg = "update2_error: " . mysqli_error($db_link);
					mysqli_rollback($db_link);
				} else {
					mysqli_commit($db_link);
				}
			}
			$return_value = array();
			if ($msg == "success") {
				$return_value['status'] = 'success';
			} else {
				$return_value['status'] = 'error';
			}
			$return_value['msg'] = $msg;

			echo json_encode($return_value, JSON_UNESCAPED_UNICODE);
			return true;
			}
	}

	if (!function_exists('education_add')) { //개별
		function education_add() {
			$msg = "success";

			$tot_user_name = $_POST['tot_user_name'];
			$edu_name = $_POST['edu_name'];
			$edu_id = $_POST['edu_id'];
			$edu_period = $_POST['edu_period'];
			$edu_startdate = $_POST['edu_startdate'];
			$edu_enddate = $_POST['edu_enddate'];
			$edu_time = $_POST['edu_time'];
			$edu_institution = $_POST['edu_institution'];
			$edu_file_yn = $_POST['edu_file_yn'];
			$edu_type = $_POST['edu_type'];
			$user_id = $_POST['user_id'];

			$db_link = db_conn();

			$sql0 = "SELECT user_name FROM eval_user WHERE user_id LIKE '%$user_id%'";
			$result = mysqli_query($db_link, $sql0);
			if ($result) {
				$row = mysqli_fetch_assoc($result);
				$user_name = $row['user_name'];
			} else {
				$msg = "select_error";
			}

			$sql1 = "INSERT INTO education_list (user_id, user_name, edu_id, edu_institution, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn) VALUES ('$user_id', '$user_name', '$edu_id', '$edu_institution', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn);";
			$update_edu = mysqli_query($db_link, $sql1);

			if(!$update_edu){
				$msg = "update_error".mysqli_error($db_link);
				mysqli_rollback($db_link);
			} else {
				$sql2 = "INSERT IGNORE INTO tb_education (edu_institution, edu_id, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn, tot_user_name) VALUES ('$edu_institution', '$edu_id', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn, '$tot_user_name');";
				$update_edu2 = mysqli_query($db_link, $sql2);
	
				if(!$update_edu2){
					$msg = $sql2."update2_error: " . mysqli_error($db_link);
					mysqli_rollback($db_link);
				} else {
					mysqli_commit($db_link);
				}
			}

			$return_value = array();
			if ($msg == "success") {
				$return_value['status'] = 'success';
			} else {
				$return_value['status'] = 'error';
			}
			$return_value['msg'] = $msg;

			echo json_encode($return_value, JSON_UNESCAPED_UNICODE);
			return true;
			}
	}

	if (!function_exists('education_edit_all')) { //개별교육 아닌 전사/임원/팀장 교육
		function education_edit_all() {
			$msg = "success";
			$edu_name = $_POST['edu_name'];
			$edu_id = $_POST['edu_id'];
			$edu_period = $_POST['edu_period'];
			$edu_startdate = $_POST['edu_startdate'];
			$edu_enddate = $_POST['edu_enddate'];
			$edu_time = $_POST['edu_time'];
			$edu_institution = $_POST['edu_institution'];
			$edu_file_yn = $_POST['edu_file_yn'];
			$edu_type = $_POST['edu_type'];

			$db_link = db_conn();

			$sql_delete = "DELETE FROM education_list WHERE edu_id = '$edu_id'";
			$delete_result = mysqli_query($db_link, $sql_delete);
			if (!$delete_result) {
				throw new Exception("delete_error: " . mysqli_error($db_link));
			}

			if($edu_type == 3){ //전사
				$sql0 = "SELECT user_id, user_name FROM eval_user";
				$result = mysqli_query($db_link, $sql0);
				if ($result) {
					$user_ids = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$user_id = $row['user_id'];
						$user_name = $row['user_name'];
						$user_ids[] = $row['user_id'];
						$sql1 = "INSERT INTO education_list (user_id, user_name, edu_id, edu_institution, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn) 
								VALUES ('$user_id', '$user_name', '$edu_id', '$edu_institution', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn);";
						$update_edu = mysqli_query($db_link, $sql1);
					}
					$total_id = implode(',', $user_ids);
				}
			} else if($edu_type == 0) { //임원
				$sql0 = "SELECT user_id, user_name FROM eval_user WHERE user_level = 0";
				$result = mysqli_query($db_link, $sql0);
				if ($result) {
					$user_ids = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$user_id = $row['user_id'];
						$user_name = $row['user_name'];
						$sql1 = "INSERT INTO education_list (user_id, user_name, edu_id, edu_institution, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn) 
								VALUES ('$user_id', '$user_name', '$edu_id', '$edu_institution', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn);";
						$update_edu = mysqli_query($db_link, $sql1);
						$user_ids[] = $row['user_id'];
					}
					$total_id = implode(',', $user_ids);
				}
			} else if($edu_type == 1) { //팀장
				$sql0 = "SELECT user_id, user_name FROM eval_user WHERE user_level = 1";
				$result = mysqli_query($db_link, $sql0);
				if ($result) {
					$user_ids = [];
					while ($row = mysqli_fetch_assoc($result)) {
						$user_id = $row['user_id'];
						$user_name = $row['user_name'];
						$user_ids[] = $row['user_id'];
						$sql1 = "INSERT INTO education_list (user_id, user_name, edu_id, edu_institution, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn) 
								VALUES ('$user_id', '$user_name', '$edu_id', '$edu_institution', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', $edu_file_yn);";
						$update_edu = mysqli_query($db_link, $sql1);
					}
					$total_id = implode(',', $user_ids);
				}
			}

			if(!$update_edu){
				$msg = "update_error".mysqli_error($db_link);
				mysqli_rollback($db_link);
			} else {
				$sql2 = "UPDATE tb_education SET 
								edu_institution = '$edu_institution',
								edu_name = '$edu_name',
								edu_startdate = '$edu_startdate',
								edu_enddate = '$edu_enddate',
								edu_period = '$edu_period',
								edu_time = '$edu_time',
								edu_type = '$edu_type',
								edu_file_yn = '$edu_file_yn',
								tot_user_name = '$total_id'
								WHERE edu_id = '$edu_id'";
				$update_edu2 = mysqli_query($db_link, $sql2);
	
				if(!$update_edu2){
					$msg = "update2_error: " . mysqli_error($db_link);
					mysqli_rollback($db_link);
				} else {
					mysqli_commit($db_link);
				}
			}

			$return_value = array();
			if ($msg == "success") {
				$return_value['status'] = 'success';
			} else {
				$return_value['status'] = 'error';
			}
			$return_value['msg'] = $msg;

			echo json_encode($return_value, JSON_UNESCAPED_UNICODE);
			return true;
			}
	}

	if (!function_exists('education_edit')) { //개별
		function education_edit() {
			$msg = "success";
			$tot_user_name = $_POST['tot_user_name'];
			$edu_name = $_POST['edu_name'];
			$edu_id = $_POST['edu_id'];
			$edu_period = $_POST['edu_period'];
			$edu_startdate = $_POST['edu_startdate'];
			$edu_enddate = $_POST['edu_enddate'];
			$edu_time = $_POST['edu_time'];
			$edu_institution = $_POST['edu_institution'];
			$edu_file_yn = $_POST['edu_file_yn'];
			$edu_type = $_POST['edu_type'];
			
			$db_link = db_conn();
			mysqli_autocommit($db_link, FALSE);

			try {
								$sql_delete = "DELETE FROM education_list WHERE edu_id = '$edu_id'";
				$delete_result = mysqli_query($db_link, $sql_delete);
				if (!$delete_result) {
					throw new Exception("delete_error: " . mysqli_error($db_link));
				}
	
				$user_names = explode(",", $tot_user_name);
				for($i = 0; $i < count($user_names); $i++){
					$user_id = trim($user_names[$i]);
					$sql_select = "SELECT user_name FROM eval_user WHERE user_id LIKE '%$user_id%'";
					$result = mysqli_query($db_link, $sql_select);
					if ($result && mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_assoc($result);
						$user_name = $row['user_name'];
	
						$sql_insert = "INSERT INTO education_list (user_id, user_name, edu_id, edu_institution, edu_name, edu_startdate, edu_enddate, edu_period, edu_time, edu_type, edu_file_yn) 
									   VALUES ('$user_id', '$user_name', '$edu_id', '$edu_institution', '$edu_name', '$edu_startdate', '$edu_enddate', '$edu_period', '$edu_time', '$edu_type', '$edu_file_yn')";
						$insert_result = mysqli_query($db_link, $sql_insert);
						if (!$insert_result) {
							throw new Exception("insert_error: " . mysqli_error($db_link));
						}
					} else {
						throw new Exception("select_error: User not found - $user_name");
					}
				}
	
				$sql_update = "UPDATE tb_education SET 
								edu_institution = '$edu_institution',
								edu_name = '$edu_name',
								edu_startdate = '$edu_startdate',
								edu_enddate = '$edu_enddate',
								edu_period = '$edu_period',
								edu_time = '$edu_time',
								edu_type = '$edu_type',
								edu_file_yn = '$edu_file_yn',
								tot_user_name = '$tot_user_name'
								WHERE edu_id = '$edu_id'";
				$update_result = mysqli_query($db_link, $sql_update);
				if (!$update_result) {
					throw new Exception("update_error: " . mysqli_error($db_link));
				}
	
				mysqli_commit($db_link);
			} catch (Exception $e) {
				mysqli_rollback($db_link);
				$msg = $e->getMessage();
			}
	
			mysqli_autocommit($db_link, TRUE);

			$return_value = array();
			if ($msg == "success") {
				$return_value['status'] = 'success';
			} else {
				$return_value['status'] = 'error';
			}
			$return_value['msg'] = $msg;

			echo json_encode($return_value, JSON_UNESCAPED_UNICODE);
			return true;
			}
	}

	if (!function_exists('change_level')) {
		function change_level()
		{
			$user_id = $_POST['user_id'];
			$user_level = $_POST['user_level'];
$user_manage = $_POST['user_manage'];

			$db_link = db_conn();

			$sql1 = "UPDATE eval_user SET user_level = '$user_level',
										  user_manage = '$user_manage'
										WHERE user_id = '$user_id'";
			$update_user = mysqli_query($db_link, $sql1);

			$msg = "수정이 완료되었습니다.";

			$return_value['msg'] = $msg;
			echo json_encode($return_value);
			return true;
		}
	}

	if (!function_exists('survey_submit')) {
		function survey_submit()
		{
			$msg = "success";
			$user_id = $_POST['user_id'];
			$edu_id = $_POST['edu_id'];
			$edu_name = $_POST['edu_name'];
			$edu_institution = $_POST['edu_institution'];
			$edu_t1 = $_POST['edu_t1'];
			$edu_t2 = $_POST['edu_t2'];
			$edu_t3 = $_POST['edu_t3'];
			$edu_t4 = $_POST['edu_t4'];
			$edu_t5 = $_POST['edu_t5'];
			$file_path = $_POST['file_path'];

			$db_link = db_conn();

			$sql_insert = "INSERT INTO education_report (user_id, edu_id, edu_institution, edu_name, edu_t1, edu_t2, edu_t3, edu_t4, edu_t5, file_path) 
									   VALUES ('$user_id', '$edu_id', '$edu_institution', '$edu_name', '$edu_t1', '$edu_t2', '$edu_t3', '$edu_t4', '$edu_t5', '$file_path')";
			$insert_result = mysqli_query($db_link, $sql_insert);

			if(!$insert_result){
				$msg = "insert_error".mysqli_error($db_link);
				mysqli_rollback($db_link);
			} else {
				$sql_update = "UPDATE education_list SET result = 1 WHERE edu_id = '$edu_id' and user_id = '$user_id'";
				$update_result = mysqli_query($db_link, $sql_update);

				if(!$update_result){
					$msg = "update_education_list_error: " . mysqli_error($db_link);
					mysqli_rollback($db_link);
				} else {
					mysqli_commit($db_link);
				}
			}

			$return_value = array();
			if ($msg == "success") {
				$return_value['status'] = 'success';
			} else {
				$return_value['status'] = 'error';
			}
			$return_value['msg'] = $msg;

			echo json_encode($return_value, JSON_UNESCAPED_UNICODE);
			return true;
		}
	}

	if (!function_exists('board_hide')) {
		function board_hide()
		{
			$msg = "success";
			$user_id = $_POST['user_id'];
			$edu_id = $_POST['edu_id'];

			$db_link = db_conn();

			$sql_update1 = "UPDATE tb_education SET tb_education.use = 0 WHERE edu_id = '$edu_id'";
			$update_result1 = mysqli_query($db_link, $sql_update1);

			if(!$update_result1){
				$msg = "update1_error".mysqli_error($db_link);
				mysqli_rollback($db_link);
			} else {
				$sql_update2 = "UPDATE education_list SET education_list.use = 0 WHERE edu_id = '$edu_id'";
				$update_result2 = mysqli_query($db_link, $sql_update2);

				if(!$update_result2){
					$msg = "update2_error: " . mysqli_error($db_link);
					mysqli_rollback($db_link);
				} else {
					mysqli_commit($db_link);
				}
			}

			$return_value = array();
			if ($msg == "success") {
				$return_value['status'] = 'success';
			} else {
				$return_value['status'] = 'error';
			}
			$return_value['msg'] = $msg;

			echo json_encode($return_value, JSON_UNESCAPED_UNICODE);
			return true;
		}
	}

	// 함수 호출 부분
	$function_name = '';
	if (isset($_POST['function']) && !empty($_POST['function'])) {
		$function_name = $_POST['function'];
	}
	
	if (function_exists($function_name)) {
		// 변수값으로 함수 호출
		if ($function_name()) {
			// 트랜잭션 완료
	//        $db->trans_commit();
		} else {
			// 트랜잭션 롤백
	//        $db->trans_rollback();
		}
	} else {
		// 함수가 존재 하지 않을 시 반환할 값
		echo json_encode(
			array(
				'function' => $function_name,
				'code' => -2,
				'msg' => '존재하지 않는 함수입니다.'
			)
		);
	}
?>
