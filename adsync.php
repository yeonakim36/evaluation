<?php
// include_once "/var/www/html/evaluation/conndb.php";
include "/var/www/web/evaluation/head.lib.php";
if(!$_SESSION['sess_userid']) { //로그인하지 않았다면 로그인 페이지로 이동
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
if($_SESSION['sess_grade'] != 1) { //관리자 권한확인
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
// mysqli_select_db($db_link,$DB_SNAME);
if (!function_exists('gosync')) {
    function gosync() {
        $db_link = db_conn();

        $update_sql_tmp = "UPDATE eval_user SET user_use = '0' WHERE user_type = '1';";
        $result_tmp = mysqli_query($db_link, $update_sql_tmp);

        $adServer = "ldap://210.102.6.151";
        $ldap = ldap_connect($adServer);
        $username = 'yeona.kim';
        $password = 'Kitty0000@@';
        $ldaprdn = 'abov' . "\\" . $username;
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        if ($ldap) {
            $bind = @ldap_bind($ldap, $ldaprdn, $password);
            if ($bind) {
                $filter="(objectClass=user)";
                $result = ldap_search($ldap,"OU=Users,OU=ABOV,dc=abov,dc=co,dc=kr",$filter);
                $info = ldap_get_entries($ldap, $result);

                for ($i = 0; $i < $info['count']; $i++) {
                    $user_id = $info[$i]["samaccountname"][0];
                    $user_name = $info[$i]["displayname"][0];
                    $user_group = $info[$i]["description"][0];
                    $user_team = $info[$i]["department"][0];

                    $SQL = " SELECT * FROM eval_user WHERE user_id = '$user_id' AND user_name = '$user_name';";
                    $result1 = mysqli_query($db_link, $SQL);

                    if ($result1->num_rows == 0) { //새로운 사용자
                        $insert_sql = "INSERT IGNORE INTO eval_user (user_id, user_name, user_group, user_team, last_login, user_use, user_type)
                                        VALUES ('$user_id', '$user_name', '$user_group', '$user_team', now(), '1', '1');";
                        $result2 = mysqli_query($db_link, $insert_sql);
                    } else { //기존사용자
                        $update_sql = "UPDATE eval_user
                                    SET user_group = '$user_group', user_team  = '$user_team', user_type = '1', user_use = '1'
                                    WHERE user_id = '$user_id' AND user_name = '$user_name' AND user_type <> 5 ;";
                        $result3 = mysqli_query($db_link, $update_sql);
                    }
                }
                 // 이메일 - $info[$i]['mail'][0], 이름 - $info[$i]['cn'][0]
                @ldap_close($ldap);

                $msg = "입력이 완료되었습니다.";
                $return_value['msg'] = $msg;
                echo json_encode($return_value);
                return true;

            } else {
                echo "LDAP 바인드 실패";
            }
        }
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

