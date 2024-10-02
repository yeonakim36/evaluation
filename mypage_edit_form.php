<?php
	include "./head.lib.php";
    include_once "./conndb.php";

	if (!function_exists('mypage_edit')) {
		function mypage_edit()
		{
			$year = $_POST['year'];
			$half = $_POST['half'];
			$total_grade = $_POST['total_grade'];
			$score = $_POST['score'];
			$user_id = $_POST['user_id'];
			$user_no = $_POST['user_no'];
			$user_rank = $_POST['user_rank'];
			$user_grade = $_POST['user_grade'];
			$user_team2 = $_POST['user_team2'];
			$etc = $_POST['etc'];

			$db_link = db_conn();

			$sql1 = "UPDATE eval_user SET user_no = '$user_no',
										user_rank ='$user_rank',
										user_grade ='$user_grade',
										user_team2 = '$user_team2'
										WHERE user_id = '$user_id'";
			$update_user = mysqli_query($db_link, $sql1);

			$sql2 = "UPDATE tb_evaluation SET total_grade ='$total_grade',
										score = '$score',
										user_no = '$user_no',
										etc = '$etc'
										WHERE year = '$year' AND half = '$half' AND user_id = '$user_id'";
			$update_eval = mysqli_query($db_link, $sql2);

			$msg = "수정이 완료되었습니다.";

			$return_value['msg'] = $msg;
			echo json_encode($return_value);
			return true;
		}
	}

	if (!function_exists('goinactive')) {
		function goinactive()
		{
			$user_id = $_POST['user_id'];

			$db_link = db_conn();

			$sql1 = "UPDATE eval_user SET user_use = '0'
									WHERE user_id = '$user_id'";
			$update_user = mysqli_query($db_link, $sql1);
	
			$msg = "수정이 완료되었습니다.";

			$return_value['msg'] = $msg;
			echo json_encode($return_value);
			return true;
		}
	}

	if (!function_exists('goactive')) {
		function goactive()
		{
			$user_id = $_POST['user_id'];

			$db_link = db_conn();

			$sql1 = "UPDATE eval_user SET user_use = '1'
									WHERE user_id = '$user_id'";
			$update_user = mysqli_query($db_link, $sql1);
	
			$msg = "수정이 완료되었습니다.";

			$return_value['msg'] = $msg;
			echo json_encode($return_value);
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
